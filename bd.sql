CREATE TABLE estudiante (
  id_estudiante SERIAL PRIMARY KEY,
  nombre_estudiante VARCHAR(25) NOT NULL
);
CREATE TABLE asignatura (
  id_asignatura SERIAL PRIMARY KEY,
  asignatura VARCHAR(25) NOT NULL
);
CREATE TABLE calificacion (
  id_calificacion SERIAL PRIMARY KEY,
  calificacion NUMERIC(2, 1),
  id_estudiante INT NOT NULL,
  id_asignatura INT NOT NULL,
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_asignatura) REFERENCES asignatura(id_asignatura)
);