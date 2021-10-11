<?php
    namespace App\Models;
    use CodeIgniter\Model;
    class UsuarioModel extends Model{
        protected $table = 'usuario';
        protected $primaryKey = 'UsuarioId';                
        protected $allowedFields = [
            'Nombre_usuario',
            'Apellido_usuario',
            'Ci_usuario',
            'Cuenta',
            'Cargo',
            'Pass'
        ];
    }