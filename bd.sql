CREATE TABLE "public"."estudiante" (
  "id_estudiante" int4 NOT NULL GENERATED BY DEFAULT AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  "nombre_estudiante" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  CONSTRAINT "estudiante_pkey" PRIMARY KEY ("id_estudiante")
);

ALTER TABLE "public"."estudiante" 
  OWNER TO "postgres";



CREATE TABLE "public"."asignatura" (
  "id_asignatura" int4 NOT NULL GENERATED BY DEFAULT AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  "asignatura" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
  CONSTRAINT "asignatura_pkey" PRIMARY KEY ("id_asignatura")
)
;

ALTER TABLE "public"."asignatura" 
  OWNER TO "postgres";




CREATE TABLE "public"."calificacion" (
  "id_calificacion" int4 NOT NULL GENERATED BY DEFAULT AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  "calificacion" numeric(10,2) NOT NULL,
  "id_estudiante" int4 NOT NULL GENERATED BY DEFAULT AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  "id_asignatura" int4 NOT NULL GENERATED BY DEFAULT AS IDENTITY (
INCREMENT 1
MINVALUE  1
MAXVALUE 2147483647
START 1
CACHE 1
),
  CONSTRAINT "calificacion_pkey" PRIMARY KEY ("id_calificacion"),
  CONSTRAINT "fk_asignatura" FOREIGN KEY ("id_asignatura") REFERENCES "public"."asignatura" ("id_asignatura") ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT "fk_estudiante" FOREIGN KEY ("id_estudiante") REFERENCES "public"."estudiante" ("id_estudiante") ON DELETE CASCADE ON UPDATE NO ACTION
)
;

ALTER TABLE "public"."calificacion" 
  OWNER TO "postgres";