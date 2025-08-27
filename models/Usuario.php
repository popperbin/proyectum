<?php
/**
 * Modelo Usuario
 * Maneja todas las operaciones relacionadas con usuarios del sistema
 */

require_once __DIR__ . '/../config/db_config.php';

class Usuario {
    private $db;
    
    // Propiedades del usuario
    public $id;
    public $nombres;
    public $apellidos;
    public $cedula;
    public $email;
    public $password;
    public $direccion;
    public $celular;
    public $cargo;
    public $rol;
    public $estado;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos($filtros = []) {
        $sql = "SELECT id, nombres, apellidos, cedula, email, direccion, celular, cargo, rol, estado, fecha_creacion FROM usuarios WHERE 1=1";
        $params = [];

        // Aplicar filtros
        if (!empty($filtros['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filtros['rol'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR email LIKE ?)";
            $termino = "%" . $filtros['buscar'] . "%";
            $params[] = $termino;
            $params[] = $termino;
            $params[] = $termino;
        }

        $sql .= " ORDER BY fecha_creacion DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        return $this->db->fetchOne($sql, [$email]);
    }

    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        // Validar datos requeridos
        $errores = $this->validarDatos($datos);
        if (!empty($errores)) {
            throw new Exception("Datos inválidos: " . implode(", ", $errores));
        }

        // Verificar que el email no esté en uso
        if ($this->obtenerPorEmail($datos['email'])) {
            throw new Exception("El correo electrónico ya está registrado");
        }

        // Verificar que la cédula no esté en uso
        $existeCedula = $this->db->fetchOne("SELECT id FROM usuarios WHERE cedula = ?", [$datos['cedula']]);
        if ($existeCedula) {
            throw new Exception("La cédula ya está registrada");
        }

        // Hash de la contraseña
        $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombres, apellidos, cedula, email, password, direccion, celular, cargo, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $datos['nombres'],
            $datos['apellidos'],
            $datos['cedula'],
            $datos['email'],
            $passwordHash,
            $datos['direccion'] ?? null,
            $datos['celular'] ?? null,
            $datos['cargo'] ?? null,
            $datos['rol'],
            $datos['estado'] ?? 'activo'
        ];

        return $this->db->insert($sql, $params);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        // Validar datos
        $errores = $this->validarDatos($datos, true);
        if (!empty($errores)) {
            throw new Exception("Datos inválidos: " . implode(", ", $errores));
        }

        // Verificar email único (excepto el usuario actual)
        if (isset($datos['email'])) {
            $existeEmail = $this->db->fetchOne("SELECT id FROM usuarios WHERE email = ? AND id != ?", [$datos['email'], $id]);
            if ($existeEmail) {
                throw new Exception("El correo electrónico ya está en uso");
            }
        }

        // Verificar cédula única (excepto el usuario actual)
        if (isset($datos['cedula'])) {
            $existeCedula = $this->db->fetchOne("SELECT id FROM usuarios WHERE cedula = ? AND id != ?", [$datos['cedula'], $id]);
            if ($existeCedula) {
                throw new Exception("La cédula ya está en uso");
            }
        }

        // Construir consulta de actualización
        $campos = [];
        $params = [];

        foreach ($datos as $campo => $valor) {
            if ($campo == 'password' && !empty($valor)) {
                $campos[] = "password = ?";
                $params[] = password_hash($valor, PASSWORD_DEFAULT);
            } elseif ($campo != 'password') {
                $campos[] = "$campo = ?";
                $params[] = $valor;
            }
        }

        if (empty($campos)) {
            throw new Exception("No hay datos para actualizar");
        }

        $params[] = $id;
        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";

        return $this->db->execute($sql, $params);
    }

    /**
     * Eliminar usuario (cambiar estado a inactivo)
     */
    public function eliminar($id) {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        // Verificar que no sea el último administrador
        if ($usuario['rol'] == 'administrador') {
            $adminCount = $this->db->fetchOne("SELECT COUNT(*) as count FROM usuarios WHERE rol = 'administrador' AND estado = 'activo'")['count'];
            if ($adminCount <= 1) {
                throw new Exception("No se puede eliminar el último administrador del sistema");
            }
        }

        $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Cambiar rol de usuario
     */
    public function cambiarRol($id, $nuevoRol) {
        $rolesPermitidos = ['administrador', 'gestor', 'colaborador', 'cliente'];
        if (!in_array($nuevoRol, $rolesPermitidos)) {
            throw new Exception("Rol no válido");
        }

        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        // Verificar que no sea el último administrador si se está cambiando de admin
        if ($usuario['rol'] == 'administrador' && $nuevoRol != 'administrador') {
            $adminCount = $this->db->fetchOne("SELECT COUNT(*) as count FROM usuarios WHERE rol = 'administrador' AND estado = 'activo'")['count'];
            if ($adminCount <= 1) {
                throw new Exception("No se puede cambiar el rol del último administrador");
            }
        }

        $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
        return $this->db->execute($sql, [$nuevoRol, $id]);
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($id, $passwordActual, $passwordNuevo) {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        // Verificar contraseña actual
        if (!password_verify($passwordActual, $usuario['password'])) {
            throw new Exception("La contraseña actual es incorrecta");
        }

        // Validar nueva contraseña
        if (strlen($passwordNuevo) < 6) {
            throw new Exception("La nueva contraseña debe tener al menos 6 caracteres");
        }

        $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        return $this->db->execute($sql, [$passwordHash, $id]);
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function obtenerEstadisticas() {
        $stats = [];

        // Total usuarios por rol
        $sql = "SELECT rol, COUNT(*) as count FROM usuarios WHERE estado = 'activo' GROUP BY rol";
        $porRol = $this->db->fetchAll($sql);
        $stats['por_rol'] = $porRol;

        // Total usuarios activos e inactivos
        $sql = "SELECT estado, COUNT(*) as count FROM usuarios GROUP BY estado";
        $porEstado = $this->db->fetchAll($sql);
        $stats['por_estado'] = $porEstado;

        // Usuarios registrados por mes (últimos 6 meses)
        $sql = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    COUNT(*) as count
                FROM usuarios 
                WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                ORDER BY mes DESC";
        $porMes = $this->db->fetchAll($sql);
        $stats['por_mes'] = $porMes;

        return $stats;
    }

    /**
     * Obtener usuarios por rol
     */
    public function obtenerPorRol($rol) {
        $sql = "SELECT id, nombres, apellidos, email, estado FROM usuarios WHERE rol = ? AND estado = 'activo'";
        return $this->db->fetchAll($sql, [$rol]);
    }

    /**
     * Autenticar usuario
     */
    public function autenticar($email, $password) {
        $usuario = $this->obtenerPorEmail($email);
        
        if (!$usuario) {
            return false;
        }

        if ($usuario['estado'] != 'activo') {
            throw new Exception("La cuenta está inactiva");
        }

        if (!password_verify($password, $usuario['password'])) {
            return false;
        }

        // Registrar último acceso
        $this->registrarUltimoAcceso($usuario['id']);

        return $usuario;
    }

    /**
     * Registrar último acceso del usuario
     */
    private function registrarUltimoAcceso($userId) {
        $sql = "UPDATE usuarios SET fecha_actualizacion = NOW() WHERE id = ?";
        $this->db->execute($sql, [$userId]);
    }

    /**
     * Validar datos de usuario
     */
    private function validarDatos($datos, $esActualizacion = false) {
        $errores = [];

        // Validaciones para creación (campos obligatorios)
        if (!$esActualizacion) {
            if (empty($datos['nombres'])) {
                $errores[] = "El nombre es obligatorio";
            }

            if (empty($datos['apellidos'])) {
                $errores[] = "Los apellidos son obligatorios";
            }

            if (empty($datos['cedula'])) {
                $errores[] = "La cédula es obligatoria";
            }

            if (empty($datos['email'])) {
                $errores[] = "El email es obligatorio";
            }

            if (empty($datos['password'])) {
                $errores[] = "La contraseña es obligatoria";
            }

            if (empty($datos['rol'])) {
                $errores[] = "El rol es obligatorio";
            }
        }

        // Validaciones de formato
        if (isset($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        if (isset($datos['password']) && !empty($datos['password']) && strlen($datos['password']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        if (isset($datos['rol'])) {
            $rolesValidos = ['administrador', 'gestor', 'colaborador', 'cliente'];
            if (!in_array($datos['rol'], $rolesValidos)) {
                $errores[] = "El rol especificado no es válido";
            }
        }

        if (isset($datos['cedula']) && !preg_match('/^[0-9]+$/', $datos['cedula'])) {
            $errores[] = "La cédula debe contener solo números";
        }

        if (isset($datos['celular']) && !empty($datos['celular']) && !preg_match('/^[0-9\-\+\(\)\s]+$/', $datos['celular'])) {
            $errores[] = "El formato del celular no es válido";
        }

        return $errores;
    }

    /**
     * Buscar usuarios
     */
    public function buscar($termino, $rol = null) {
        $sql = "SELECT id, nombres, apellidos, email, rol, estado 
                FROM usuarios 
                WHERE (nombres LIKE ? OR apellidos LIKE ? OR email LIKE ?) 
                AND estado = 'activo'";
        
        $params = ["%$termino%", "%$termino%", "%$termino%"];

        if ($rol) {
            $sql .= " AND rol = ?";
            $params[] = $rol;
        }

        $sql .= " ORDER BY nombres ASC LIMIT 20";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obtener proyectos del usuario (como gestor, colaborador o cliente)
     */
    public function obtenerProyectos($userId) {
        $sql = "SELECT DISTINCT p.*, 
                       CASE 
                           WHEN p.gestor_id = ? THEN 'Gestor'
                           WHEN p.cliente_id = ? THEN 'Cliente'
                           WHEN t.asignado_a = ? THEN 'Colaborador'
                           ELSE 'Sin rol'
                       END as rol_en_proyecto
                FROM proyectos p
                LEFT JOIN tareas t ON p.id = t.proyecto_id
                WHERE p.gestor_id = ? OR p.cliente_id = ? OR t.asignado_a = ?
                ORDER BY p.fecha_creacion DESC";
        
        return $this->db->fetchAll($sql, [$userId, $userId, $userId, $userId, $userId, $userId]);
    }
}