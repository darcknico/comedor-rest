create or replace function fn_comprar_ticket() returns trigger as
$$
consulta = plpy.prepare("""
select
	usu_saldo,usu_tickets
from
	tbl_usuarios
where
	usu_id = $1
""",["int"])
usuario = TD["new"]["usu_id"]
menu = TD["new"]["men_id"]
rs = plpy.execute(consulta,[usuario])
saldo = rs[0]["usu_saldo"]
tickets = rs[0]["usu_tickets"]
consulta = plpy.prepare("""
select
	count(tic_id) as contador
from
	tbl_tickets
where
	usu_id = $1 and
	men_id = $2
""",["int","int"])
rs = plpy.execute(consulta,[usuario,menu])
existe = rs[0]["contador"]
consulta = plpy.prepare("""
select
	men_restantes
from
	tbl_menus
where
	men_id = $1
""",["int"])
rs = plpy.execute(consulta,[menu])
restantes = rs[0]["men_restantes"]
if(int(existe)==1):
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D000M'})
	plpy.error("Usted ya compro un ticket para el dia "+TD["new"]["tic_fecha"])
if(float(saldo)<float(TD["new"]["tic_precio"])):
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D001M'})
	plpy.error("No posee suficiente saldo $"+str(saldo),sqlstate=1001)
if(int(tickets)<1):
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D002M'})
	plpy.error("Ya uso todos los tickets")
if(int(restantes)<1):
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D003M'})
	plpy.error("No hay tickets disponibles para la venta del dia "+TD["new"]["tic_fecha"])
if(float(saldo)>float(TD["new"]["tic_precio"]) and int(tickets)>0 and int(restantes)>0):
	consulta = plpy.prepare("""
	update
		tbl_menus
	set
		men_comprados = men_comprados + 1,
		men_restantes = men_restantes - 1
	where
		men_id = $1
	""",["int"])
	plpy.execute(consulta,[menu])
	consulta = plpy.prepare("""
	update
		tbl_usuarios
	set
		usu_saldo = usu_saldo - $1,
		usu_tickets = usu_tickets - 1
	where
		usu_id = $2
	""",["decimal","int"])
	plpy.execute(consulta,[TD["new"]["tic_precio"],usuario])
	return "OK"
else:
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D004M'})
	plpy.error("Terminacion imprevista")
return "SKIP"
$$
language plpython3u;

create or replace function generar_codigo() returns trigger as
$$
from datetime import datetime
consulta = plpy.prepare("""
update
	tbl_tickets
set
	tic_codigo = $1
where
	tic_id = $2
""",["varchar","int"])

parteA = str(TD["new"]["usu_id"]).zfill(6)
fecha = datetime.strptime(str(TD["new"]["tic_fecha"]),'%Y-%m-%d')
parteB = fecha.strftime('%y%m%d').zfill(6)
ticket = TD["new"]["tic_id"]
parteC = str(ticket).zfill(6)
plpy.execute(consulta,[parteA+parteB+parteC,ticket])
$$
language plpython3u;

create or replace function fn_cancelar_ticket() returns trigger as
$$
estadoOld = TD["old"]["tic_estado"]
estadoNew = TD["new"]["tic_estado"]
menu = TD["new"]["men_id"]
precio = TD["new"]["tic_precio"]
if(estadoOld=="cancelado" and estadoNew=="cancelado"):
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D000M'})
	plpy.error("No puede cancelar este ticket")
if(estadoOld=="activo" and estadoNew=="cancelado"):
	consulta = plpy.prepare("""
	update
		tbl_menus
	set
		men_comprados = men_comprados - 1,
		men_restantes = men_restantes + 1
	where
		men_id = $1
	""",["int"])
	plpy.execute(consulta,[menu])
	consulta = plpy.prepare("""
	update
		tbl_usuarios
	set
		usu_saldo = usu_saldo + $1,
		usu_tickets = usu_tickets + 1
	where
		usu_id = $2
	""",["decimal","int"])
	plpy.execute(consulta,[precio,usuario])
	return "OK"
if(estadoOld=="vencido" and estadoNew=="cancelado"):
	consulta = plpy.prepare("""
	update
		tbl_usuarios
	set
		usu_tickets = usu_tickets + 1
	where
		usu_id = $1
	""",["int"])
	plpy.execute(consulta,[usuario])
	return "OK"
else:
	raise type('MyError', (plpy.SPIError,), {'sqlstate': 'D001M'})
	plpy.error("Terminacion imprevista")
return "SKIP"
$$
language plpython3u;
