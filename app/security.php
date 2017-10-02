<?php

 /**
  * @SWG\SecurityScheme(
  *   securityDefinition="comedor_auth",
  *   type="oauth2",
  *   authorizationUrl="url de endpoint de autorizacion",
  *   tokenUrl="url para el endpoint de token",
  *   flow="password",
  *   scopes={
  *     "basico": "Alumno",
  *     "medio": "?",
  *     "completo": "Administrador"
  *   }
  * )
  */
