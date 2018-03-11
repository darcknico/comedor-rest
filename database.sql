CREATE TABLE public.tbl_usuarios
(
   usu_id serial NOT NULL,
   usu_dni bigint,
   usu_nombre character varying(126),
   usu_apellido character varying(126),
   "usu_contraseña" character varying(126),
   modificado timestamp without time zone,
   creado timestamp without time zone,
   estado boolean DEFAULT true,
   tus_id integer,
   usu_saldo numeric(10,2),
   usu_tickets integer DEFAULT 7,
   usu_estado character varying(64),
   usu_imagen character varying(126),
   CONSTRAINT pk_usu_id PRIMARY KEY (usu_id)
)
WITH (
  OIDS = FALSE
)
;
ALTER TABLE public.tbl_usuarios
  OWNER TO rlera;

CREATE TABLE public.tbl_tipos_usuario
(
  tus_id integer NOT NULL DEFAULT nextval('tbl_tipos_usuario_tus_id_seq'::regclass),
  tus_titulo character varying(64),
  CONSTRAINT pk_tus_id PRIMARY KEY (tus_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.tbl_tipos_usuario
  OWNER TO rlera;

CREATE TABLE public.tbl_menus
(
   men_id serial NOT NULL,
   men_fecha timestamp without time zone,
   men_descripcion character varying(126),
   men_precio numeric(10,2) DEFAULT 5,
   men_cantidad integer DEFAULT 300,
   men_comprados integer DEFAULT 0,
   men_restantes integer DEFAULT 300,
   modificado timestamp without time zone,
   creado timestamp without time zone,
   men_validados integer DEFAULT 0,
   men_finalizado boolean DEFAULT false,
   estado boolean DEFAULT true,
   CONSTRAINT pk_men_id PRIMARY KEY (men_id)
)
WITH (
  OIDS = FALSE
)
;
ALTER TABLE public.tbl_menus
  OWNER TO rlera;

  CREATE TABLE public.tbl_tickets
  (
     tic_id serial NOT NULL,
     tic_precio numeric(10,2),
     tic_codigo character varying(128),
     men_id integer NOT NULL,
     usu_id integer NOT NULL,
     estado boolean DEFAULT true,
     creado timestamp without time zone,
     modificado timestamp without time zone,
     tic_fecha timestamp without time zone,
     tic_estado character varying(126) DEFAULT 'activo',
     CONSTRAINT pk_tic_id PRIMARY KEY (tic_id),
     CONSTRAINT fk_menus FOREIGN KEY (men_id) REFERENCES public.tbl_menus (men_id) ON UPDATE NO ACTION ON DELETE NO ACTION,
     CONSTRAINT fk_usuarios FOREIGN KEY (usu_id) REFERENCES public.tbl_usuarios (usu_id) ON UPDATE NO ACTION ON DELETE NO ACTION
  )
  WITH (
    OIDS = FALSE
  )
  ;
ALTER TABLE public.tbl_tickets
  OWNER TO rlera;
CREATE TABLE public.tbl_transacciones
(
   tra_id serial NOT NULL,
   usu_id integer NOT NULL,
   tra_concepto character varying(126),
   pay_id character varying(126),
   tra_monto numeric(10,2),
   tra_fecha timestamp without time zone,
   creado timestamp without time zone,
   modificado timestamp without time zone,
   tra_estado character varying(32) DEFAULT 'pendiente',
   CONSTRAINT pk_tra_id PRIMARY KEY (tra_id)
)
WITH (
  OIDS = FALSE
)
;
ALTER TABLE public.tbl_transacciones
  OWNER TO rlera;

CREATE TABLE oauth_clients (
  client_id             VARCHAR(80)   NOT NULL,
  client_secret         VARCHAR(80),
  redirect_uri          VARCHAR(2000),
  grant_types           VARCHAR(80),
  scope                 VARCHAR(4000),
  user_id               VARCHAR(80),
  PRIMARY KEY (client_id)
);

CREATE TABLE oauth_access_tokens (
  access_token         VARCHAR(40)    NOT NULL,
  client_id            VARCHAR(80)    NOT NULL,
  user_id              VARCHAR(80),
  expires              TIMESTAMP      NOT NULL,
  scope                VARCHAR(4000),
  PRIMARY KEY (access_token)
);

CREATE TABLE oauth_authorization_codes (
  authorization_code  VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  redirect_uri        VARCHAR(2000),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  id_token            VARCHAR(1000),
  PRIMARY KEY (authorization_code)
);

CREATE TABLE oauth_refresh_tokens (
  refresh_token       VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  PRIMARY KEY (refresh_token)
);

CREATE TABLE oauth_users (
  username            VARCHAR(80),
  password            VARCHAR(80),
  first_name          VARCHAR(80),
  last_name           VARCHAR(80),
  email               VARCHAR(80),
  email_verified      BOOLEAN,
  scope               VARCHAR(4000),
  PRIMARY KEY (username)
);

CREATE TABLE oauth_scopes (
  scope               VARCHAR(80)     NOT NULL,
  is_default          BOOLEAN,
  PRIMARY KEY (scope)
);

CREATE TABLE oauth_jwt (
  client_id           VARCHAR(80)     NOT NULL,
  subject             VARCHAR(80),
  public_key          VARCHAR(2000)   NOT NULL
);

INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ('testclient', 'testpass', 'http://fake/');
INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ('ClienteAndroid', 'ClienteAndroid', 'http://fake/');
