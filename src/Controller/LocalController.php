<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PDO;

class LocalController extends AbstractController
{
    private function obtenerConexion(): PDO
    {
        $host = '172.31.231.10'; 
        $dbname = 'Prueba'; 
        $usuario = 'postgres'; 
        $contraseña = ''; 

        $dsn = "pgsql:host=$host;dbname=$dbname;user=$usuario;password=$contraseña";
        
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    private function obtenerEstudiantes(): array
    {
    $pdo = $this->obtenerConexion();

    $query = "SELECT c.id_calificacion, c.calificacion, a.asignatura, e.id_estudiante, e.nombre_estudiante
        FROM calificacion c
        INNER JOIN asignatura a ON c.id_asignatura = a.id_asignatura
        INNER JOIN estudiante e ON c.id_estudiante = e.id_estudiante";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
        public function home (Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nombreEstudiante = $request->request->get('nombre_estudiante');
            $calificacion = $request->request->get('calificacion');
        
            // Verificar si el nombre del estudiante ya existe
            $pdo = $this->obtenerConexion();
            $verificarEstudiante = $pdo->prepare("SELECT COUNT(*) FROM estudiante WHERE LOWER(TRIM(nombre_estudiante)) = LOWER(TRIM(:nombre_estudiante))");
            $verificarEstudiante->bindParam(':nombre_estudiante', $nombreEstudiante);
            $verificarEstudiante->execute();

            if ($verificarEstudiante->fetchColumn() > 0) {
            // Redirigir de nuevo a la ruta con un mensaje de error
            return $this->render('home.html.twig', [
            'error' => 'El estudiante ya existe.',
            'estudiante' => $this->obtenerEstudiantes(),
            ]);
}
        
            // Insertar nuevo estudiante
            $sqlInsert = "INSERT INTO estudiante (nombre_estudiante) VALUES (:nombre_estudiante)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':nombre_estudiante', $nombreEstudiante);
            $stmtInsert->execute();
        
            // Insertar la calificación
            $idEstudiante = $pdo->lastInsertId();
            $idAsignatura = 1; 
            $sqlCalificacionInsert = "INSERT INTO calificacion (id_estudiante, id_asignatura, calificacion) VALUES (:id_estudiante, :id_asignatura, :calificacion)";
            $stmtCalificacionInsert = $pdo->prepare($sqlCalificacionInsert);
            $stmtCalificacionInsert->bindParam(':id_estudiante', $idEstudiante);
            $stmtCalificacionInsert->bindParam(':id_asignatura', $idAsignatura);
            $stmtCalificacionInsert->bindParam(':calificacion', $calificacion);
            $stmtCalificacionInsert->execute();
        
            return $this->redirectToRoute('home');
        }

    try {
        $estudiantes = $this->obtenerEstudiantes();
        return $this->render('home.html.twig', [
            'estudiante' => $estudiantes,
            'error' => null, 
        ]);
        } catch (\PDOException $e) {
        return new Response('Error de conexión: ' . $e->getMessage());
        }
    }

    #[Route('/calificacion/modificar/{id}', name: 'modificar_calificacion', methods: ['POST'])]
    public function modificarCalificacion(Request $request, int $id): Response
    {
        $pdo = $this->obtenerConexion();
    
        // Obtener la calificación del estudiante por ID
        $query = "SELECT * FROM calificacion WHERE id_calificacion = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $calificacion = $stmt->fetch();
    
        if (!$calificacion) {
            return new Response('No se encontró la calificación.', 404);
        }
    
        if ($request->isMethod('POST')) {
            // Obtener la nueva calificación del formulario
            $nuevaCalificacion = $request->request->get('calificacion');
    
            // Actualizar la calificación en la base de datos
            $updateQuery = "UPDATE calificacion SET calificacion = :calificacion WHERE id_calificacion = :id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':calificacion', $nuevaCalificacion);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $updateStmt->execute();
    
            // Redirigir a la página principal
            return $this->redirectToRoute('home');
        }
    
        // vista para modificar la calificación
        return $this->render('modificar_calificacion.html.twig', [
            'calificacion' => $calificacion,
        ]);
    }

    #[Route('/estudiante/eliminar/{id}', name: 'eliminar_estudiante')]
    public function eliminarEstudiante(int $id): Response
    {
        $pdo = $this->obtenerConexion();

        // Eliminar el estudiante de la base de datos
        $sqlDelete = "DELETE FROM estudiante WHERE id_estudiante = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtDelete->execute();
    
        return $this->redirectToRoute('home'); 
    }
}
