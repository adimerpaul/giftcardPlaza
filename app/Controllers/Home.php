<?php

namespace App\Controllers;
use App\Libraries\GroceryCrud;
use App\Models\UsuarioModel;

class Home extends BaseController
{
    private $db;
    private $usuario = '' ;
    public $nomusuario='';
    public $apeusuario='';
    public $userFoother='';    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usuario = new UsuarioModel();        
        $this->EscPos;
		helper(['url', 'form']);
    }

    public function index(){
        //Accesos de Usuario        
        $session = session();
        $session->setFlashdata('msg', '');
        return view('login');
    }
    
    public function login(){
        $session = session();
        $nombre_usuario =$this->request->getVar('usuario');
        $password =md5($this->request->getVar('password'));
        $data = array('Cuenta'=>$nombre_usuario,'Pass'=>$password);       
        $datos_usuario =  $this->usuario->where($data);           
        $rows = $this->usuario->countAllResults();        
        if($rows==1){
            $datos_usuario =  $this->usuario->where($data);       
            $datos_usuario = $datos_usuario->first();            
            $datos =[
                'isLoggedIn' => TRUE,
                'id' => $datos_usuario['UsuarioId'],
                'Nombre' =>  $datos_usuario['Nombre_usuario'],
                'Apellido' =>  $datos_usuario['Apellido_usuario'],
                'CI' =>  $datos_usuario['Ci_usuario'],
                'Cuenta' =>  $datos_usuario['Cuenta'],
                'Password' =>  $datos_usuario['Pass'],
                'Cargo' =>  $datos_usuario['Cargo']
            
            ];
           
            $session->set($datos);            
            if($datos_usuario['Cargo']=="Administrador"){
             
                return redirect()->to('/inicio');
            }
            else{
               
                return redirect()->to('/inicioCajero');
            }
        }else{
            $session->setFlashdata('msg', 'Error en Usuario o Contraseña'.$nombre_usuario);
            return view('login');
        } 
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }

    public function test(){
        
      $builder2 = $this->db->table('cargartarjeta');
            $builder2->select('N_recibor');
            $builder2->orderBy('CargartarjetaID', 'DESC');
            $builder2->limit(1);
            $data = $builder2->get()->getRow('N_recibor');
            print_r($data);               
            
    }

    public function inicioCajero()
    {
        
        $builder = $this->db->table('cliente');
        $query   = $builder->get();
        $lista_clientes = [];
        foreach ($query->getResult() as $row) {
            $lista_clientes[$row->id] = $row->Nombre." ".$row->Apellido;
        }
        if(count($lista_clientes)==0){            
            return redirect()->to('/cliente'); 
        }else{            
            $crud = new GroceryCrud();
            $crud->setLanguage("spanish");        
            $crud->unsetDelete();
            $crud->unsetEdit();
            $crud->setTheme('datatables');
            $crud->setTable('tarjeta');        
            $crud->setSubject('Tarjeta', 'Tarjetas');
            $crud->columns(['Codigo_tarjeta', 'Fechacaducidad', 'Saldo', 'ClienteID']);

            $crud->displayAs('Codigo_tarjeta', 'Codigo Tarjeta');
            $crud->displayAs('Fechacaducidad', 'Fecha Caducidad');
            $crud->displayAs('Saldo', 'Saldo');
            $crud->displayAs('ClienteID', 'Cliente' );

            $crud->fieldType('Codigo_tarjeta','hidden');
            $crud->fieldType('Fechacaducidad','date');
            $crud->fieldType('Saldo','numeric');        
            $crud->fieldType('ClienteID','dropdown', $lista_clientes);
            //$crud->fields(['Fechacaducidad', 'Saldo', 'ClienteID']);


            $crud->setRule('Fechacaducidad', 'Fecha Caducidad', 'trim|max_length[255]|required',["required"=>"Fecha Caducidad es Campo Obligatorio"]);
            $crud->setRule('Saldo', 'Saldo', 'trim|max_length[255]|required',["required"=>"Saldo es Campo Obligatorio"]);
            $crud->setRule('ClienteID', 'Cliente', 'trim|max_length[255]|required',["required"=>"Cliente es Campo Obligatorio"]);
            $crud->requiredFields(['Fechacaducidad', 'Saldo', 'ClienteID']);
            if(count($lista_clientes)==1){
                $crud->setActionButton('Recargar', 'fa fa-money-bill-alt', function ($row) {
                    return base_url().'/recargarTarjeta/' . $row;
                }, false);
    
                $crud->setActionButton('Cobrar', 'fas fa-credit-card', function ($row) {
                    return base_url().'/cobrarTarjeta/' . $row;
                }, false);
                
            }  
         

            $crud->callbackBeforeInsert(function ($stateParameters) {
                $builder = $this->db->table('cliente');
                $builder->where('id', $stateParameters->data['ClienteID']);
                $cliente = $builder->get()->getRow();
                $nombre_cliente = $cliente->Nombre;
                $v = explode(" ",$nombre_cliente);
                $cod = "";
                foreach($v as $item){
                    $cod = $cod.strtolower($item[0]);
                }
                
                $apellido_cliente = $cliente->Apellido;
                $v = explode(" ",$apellido_cliente);                
                foreach($v as $item){
                    $cod = $cod.strtolower($item[0]);
                }
                $ci_cliente = strrev($cliente->Ci_nit);
                $builder = $this->db->table('tarjeta');
                $tarjeta = $builder->get();
                $numero_tarjeta = count($tarjeta->getResult());
                $stateParameters->data['Codigo_tarjeta'] = $cod."-".$ci_cliente."-".$numero_tarjeta;
                return $stateParameters;
            });

            $output = $crud->render();
            return view('cajero', (array)$output);
        }
    }
    public function inicio(){
        //Recuperar Lista de Clientes
        $builder = $this->db->table('cliente');
        $query   = $builder->get();
        $lista_clientes = [];
        foreach ($query->getResult() as $row) {
            $lista_clientes[$row->id] = $row->Nombre." ".$row->Apellido;
        }
        if(count($lista_clientes)==0){            
            return redirect()->to('/cliente'); 
        }else{            
            $crud = new GroceryCrud();
            $crud->setLanguage("spanish");        
            $crud->unsetDelete();
            $crud->unsetEdit();
            $crud->setTheme('datatables');
            $crud->setTable('tarjeta');        
            $crud->setSubject('Tarjeta', 'Tarjetas');
            $crud->columns(['Codigo_tarjeta', 'Fechacaducidad', 'Saldo', 'ClienteID']);

            $crud->displayAs('Codigo_tarjeta', 'Codigo Tarjeta');
            $crud->displayAs('Fechacaducidad', 'Fecha Caducidad');
            $crud->displayAs('Saldo', 'Saldo');
            $crud->displayAs('ClienteID', 'Cliente' );

            $crud->fieldType('Codigo_tarjeta','hidden');
            $crud->fieldType('Fechacaducidad','date');
            $crud->fieldType('Saldo','numeric');        
            $crud->fieldType('ClienteID','dropdown', $lista_clientes);
            //$crud->fields(['Fechacaducidad', 'Saldo', 'ClienteID']);


            $crud->setRule('Fechacaducidad', 'Fecha Caducidad', 'trim|max_length[255]|required',["required"=>"Fecha Caducidad es Campo Obligatorio"]);
            $crud->setRule('Saldo', 'Saldo', 'trim|max_length[255]|required',["required"=>"Saldo es Campo Obligatorio"]);
            $crud->setRule('ClienteID', 'Cliente', 'trim|max_length[255]|required',["required"=>"Cliente es Campo Obligatorio"]);
            $crud->requiredFields(['Fechacaducidad', 'Saldo', 'ClienteID']); 
            $crud->setActionButton('Recargar', 'fa fa-money-bill-alt', function ($row) {
                return base_url().'/recargarTarjeta/' . $row;
            }, false);

            $crud->setActionButton('Cobrar', 'fas fa-credit-card', function ($row) {
                return base_url().'/cobrarTarjeta/' . $row;
            }, false);
            $crud->setActionButton('Anular', 'fas fa-ban', function ($row) {
                return base_url().'/anularTarjeta/' . $row;
            }, false);

            $crud->callbackBeforeInsert(function ($stateParameters) {
                $builder = $this->db->table('cliente');
                $builder->where('id', $stateParameters->data['ClienteID']);
                $cliente = $builder->get()->getRow();
                $nombre_cliente = $cliente->Nombre;
                $v = explode(" ",$nombre_cliente);
                $cod = "";
                foreach($v as $item){
                    $cod = $cod.strtolower($item[0]);
                }
                
                $apellido_cliente = $cliente->Apellido;
                $v = explode(" ",$apellido_cliente);                
                foreach($v as $item){
                    $cod = $cod.strtolower($item[0]);
                }
                $ci_cliente = strrev($cliente->Ci_nit);
                $builder = $this->db->table('tarjeta');
                $tarjeta = $builder->get();
                $numero_tarjeta = count($tarjeta->getResult());
                $stateParameters->data['Codigo_tarjeta'] = $cod."-".$ci_cliente."-".$numero_tarjeta;
                return $stateParameters;
            });

            $output = $crud->render();
            return view('index', (array)$output);
        }
        
    }

    public function usuario($operation = null){
        $crud = new GroceryCrud();
        $crud->setLanguage("spanish");  
        $crud->setTheme('datatables');
        $crud->unsetDelete();      
	    $crud->setTable('usuario');
        $crud->setSubject('Usuario', 'Usuarios');
        $crud->columns(['Nombre_usuario', 'Apellido_usuario', 'Ci_usuario', 'Cuenta', 'Cargo', 'Tipousuario']);

        $crud->displayAs('Nombre_usuario', 'Nombre');
        $crud->displayAs('Apellido_usuario', 'Apellido');
        $crud->displayAs('Ci_usuario', 'CI');
        $crud->displayAs('Cuenta', 'Cuenta de Usuario');
        $crud->displayAs('Cargo', 'Cargo');
        $crud->displayAs('Pass', 'Password');
        $crud->displayAs('Tipousuario', 'Tipo de Usuario');
        
        $crud->fieldType('Nombre_usuario','string');
        $crud->fieldType('Apellido_usuario','string');
        $crud->fieldType('Ci_usuario','integer');
        $crud->fieldType('Cuenta','string');
        $crud->fieldType('Cargo','string');
        $crud->fieldType('Pass','password');        
        $crud->fieldType('Tipousuario','dropdown', array('1' => 'Administrador', '2' => 'Cajero'));

        $crud->setRule('Nombre_usuario', 'Nombre', 'trim|max_length[255]|required',["required"=>"Nombre es Campo Obligatorio"]);
        $crud->setRule('Apellido_usuario', 'Apellido', 'trim|max_length[255]|required',["required"=>"Apellido es Campo Obligatorio"]);
        $crud->setRule('Ci_usuario', 'CI', 'trim|max_length[255]|required|integer',["required"=>"Ci es Campo Obligatorio", "integer"=>"CI debe ser un Numero"]);
        if( $operation == 'insert_validation' || $operation == 'insert'){
            $crud->setRule('Cuenta', 'Cuenta de Usuario', 'trim|max_length[255]|required|is_unique[usuario.Cuenta]',["required"=>"Cuenta es Campo Obligatorio", "is_unique" =>"Cuenta de Usuario ya existente."]);
        }
        else{
            $crud->setRule('Cuenta', 'Cuenta de Usuario', 'trim|max_length[255]|required',["required"=>"Cuenta es Campo Obligatorio"]);
        }
        
        $crud->setRule('Cargo', 'Cargo', 'trim|max_length[255]|required', ["required"=>"Cargo es Campo Obligatorio"]);
        $crud->setRule('Pass', 'Password', 'trim|max_length[255]|required',["required"=>"Password es Campo Obligatorio"]);
        $crud->setRule('Tipousuario', 'Tipo de Usuario', 'required', ["required"=>"Tipo Usuario es Campo Obligatorio"]);
        
        $crud->requiredFields(['Nombre_usuario', 'Apellido_usuario', 'Ci_usuario', 'Cuenta', 'Cargo', 'Pass', 'Tipousuario']); 
        $crud->callbackBeforeInsert(function ($stateParameters) {
            $stateParameters->data['Pass'] = md5($stateParameters->data['Pass']);        
            return $stateParameters;
        });        

        $crud->callbackBeforeUpdate(function ($stateParameters) {
            $stateParameters->data['Pass'] = md5($stateParameters->data['Pass']);        
            return $stateParameters;
        });
        
	    $output = $crud->render();
        return view('register', (array)$output);
    }

    public function cliente(){
        $crud = new GroceryCrud();
        //$crud->setApiUrlPath(base_url().'/Home/registrarCliente');
        $crud->setLanguage("spanish");       
        $crud->setTheme('datatables');
        $crud->unsetDelete(); 
	    $crud->setTable('cliente');        

        $crud->setSubject('Cliente', 'Clientes');
        $crud->columns(['Nombre', 'Apellido', 'Cel', 'Ci_nit', 'Direccion', 'Fecha_nac', 'Email']);

        $crud->displayAs('Nombre', 'Nombre');
        $crud->displayAs('Apellido', 'Apellido');
        $crud->displayAs('Cel', 'Numero Celular');
        $crud->displayAs('Ci_nit', 'CI/NIT');
        $crud->displayAs('Direccion', 'Direccion');
        $crud->displayAs('Fecha_nac', 'Fecha Nacimiento');
        $crud->displayAs('Email', 'Correo Electrónico');        
        
        $crud->fieldType('Nombre','string');
        $crud->fieldType('Apellido','string');
        $crud->fieldType('Cel','integer');
        $crud->fieldType('Ci_nit','integer');
        $crud->fieldType('Direccion','string');
        $crud->fieldType('Fecha_nac','date');
        $crud->fieldType('Email','email');                

        $crud->setRule('Nombre', 'Nombre', 'trim|max_length[255]|required',["required"=>"Nombre es Campo Obligatorio"]);
        $crud->setRule('Apellido', 'Apellido', 'trim|max_length[255]|required',["required"=>"Apellido es Campo Obligatorio"]);
        $crud->setRule('Cel', 'Celular', 'trim|max_length[255]|required|integer',["required"=>"Numero de Celuar es Campo Obligatorio", "integer"=>"CI debe ser un Numero"]);
        $crud->setRule('Ci_nit', 'CI/Nit', 'trim|max_length[255]|required', ["required"=>"CI/NIT es Campo Obligatorio"]);
        $crud->setRule('Direccion', 'Direccion', 'trim|max_length[255]|required',["required"=>"Direccion es Campo Obligatorio"]);
        $crud->setRule('Fecha_nac', 'Fecha Nacimiento', 'required', ["required"=>"Fecha de Nacimiento es Campo Obligatorio"]);
        $crud->setRule('Email', 'Correo Electronico', 'required|valid_email', ["required"=>"Email es Campo Obligatorio", "valid_email"=>"Correo Electronico no tiene el formato requerido"]);        
        
        $crud->requiredFields(['Nombre', 'Apellido', 'Cel', 'Ci_nit', 'Direccion', 'Fecha_nac', 'Email']);         

	    $output = $crud->render();
        return view('registrarCliente',(array)$output);
    }

    public function recargarTarjeta($id_tarjeta){
        $builder = $this->db->table('tarjeta');
        $builder->where('TarjetaID', $id_tarjeta);
        $tarjeta= $builder->get()->getRow();
        
        $crud = new GroceryCrud();       
        $crud->setLanguage("spanish");       
        $crud->setTheme('datatables');
        $crud->unsetDelete();
        $crud->unsetEdit();
	    $crud->setTable('cargartarjeta');

        $crud->setSubject('Recarga', 'Recargas');
        $crud->columns(['Fecha_carga', 'Hora_carga', 'Monto_recarga', 'Porcentaje_beneficiario', 'N_recibor', 'Lugar_recibor']);

        $crud->displayAs('Fecha_carga', 'Fecha de Carga');
        $crud->displayAs('Hora_carga', 'Hora de Carga');
        $crud->displayAs('Monto_recarga', 'Monto recarga');
        $crud->displayAs('Porcentaje_beneficiario', 'Porcentaje de Beneficio');
        $crud->displayAs('N_recibor', 'Numero Recibo');
        $crud->displayAs('Lugar_recibor', 'Lugar de Recarga');
        $crud->displayAs('TarjetaID', 'ID Tarjeta');

        $crud->fieldType('Fecha_carga','string');
        $crud->fieldType('Hora_carga','time');
        $crud->fieldType('Monto_recarga','integer');
        $crud->fieldType('Porcentaje_beneficiario','integer');
        $crud->fieldType('N_recibor','hidden');
        $crud->fieldType('Lugar_recibor','string');
        $crud->fieldType('TarjetaID','hidden');

       $crud->setRule('Fecha_carga', 'Fecha de Carga', 'trim|max_length[255]|required',["required"=>"Fecha de Carga es Campo Obligatorio"]);
       $crud->setRule('Hora_carga', 'Hora de Carga', 'trim|max_length[255]|required',["required"=>"Hora de Carga es Campo Obligatorio"]);
       $crud->setRule('Monto_recarga', 'Monto recarga', 'trim|max_length[255]|required|integer',["required"=>"Monto de Recarga es Campo Obligatorio", "integer"=>"Monto de Recarga debe ser un Numero"]);
       $crud->setRule('Porcentaje_beneficiario', 'Porcentaje de Beneficio', 'trim|max_length[255]|required', ["required"=>"Porcentaje Beneficiario es Campo Obligatorio"]);       
       $crud->setRule('Lugar_recibor', 'Lugar de Recibo', 'required', ["required"=>"Lugar de Recibo es Campo Obligatorio"]);
       
       $crud->callbackBeforeInsert(function ($stateParameters) {
            $stateParameters->data['Monto_recarga']=$stateParameters->data['Monto_recarga']+ ($stateParameters->data['Monto_recarga']*($stateParameters->data['Porcentaje_beneficiario']/100.0));
            $builder = $this->db->table('tarjeta');            
            $builder->set('Saldo', 'Saldo + '.$stateParameters->data['Monto_recarga'], false);
            $builder->where('TarjetaID', $stateParameters->data['TarjetaID']);
            $builder->update();
            $builder2 = $this->db->table('cargartarjeta');
            $builder2->select('N_recibor');
            $builder2->orderBy('CargartarjetaID', 'DESC');
            $builder2->limit(1);
            $data = $builder2->get()->getRow('N_recibor');
            $stateParameters->data['N_recibor']=$data +1;
            return $stateParameters;
        });        

       $crud->requiredFields(['Fecha_carga', 'Hora_carga', 'Monto_recarga', 'Porcentaje_beneficiario', 'Lugar_recibor']);
  
       $crud->where("cargartarjeta.TarjetaID = $id_tarjeta");
        $crud->setActionButton('Imprimir Boleta', 'fa fa-money-bill-alt', function ($row) {
            return base_url().'/imprimirRecarga/' . $row;
        }, true);
        
       $output = $crud->render();    
       $data['tarjeta'] = $tarjeta;        
       $data['output'] = $output;
       return view('recargar', $data);        
    }

    public function cobrarTarjeta($id_tarjeta){
        $builder = $this->db->table('tarjeta');
        $builder->where('TarjetaID', $id_tarjeta);
        $tarjeta= $builder->get()->getRow();
        
        $crud = new GroceryCrud();       
        $crud->setLanguage("spanish");       
        $crud->setTheme('datatables');
        $crud->unsetDelete();
        $crud->unsetEdit();
	    $crud->setTable('cobrartarjeta');        

        $crud->setSubject('Cobro', 'Cobros');
        $crud->columns(['Fecha_cobro', 'Hora_cobro', 'Monto_cobro', 'N_factura', 'N_reciboc', 'Lugar_reciboc']);

        $crud->displayAs('Fecha_cobro', 'Fecha de Cobro');
        $crud->displayAs('Hora_cobro', 'Hora de Cobro');
        $crud->displayAs('Monto_cobro', 'Monto de Cobro');
        $crud->displayAs('N_factura', 'Numero de Factura');
        $crud->displayAs('N_reciboc', 'Numero Recibo');
        $crud->displayAs('Lugar_reciboc', 'Lugar de Recarga');
        $crud->displayAs('TarjetaID', 'ID Tarjeta');

        $crud->fieldType('Fecha_cobro','stringdate');        
        $crud->fieldType('Hora_cobro','time');
        $crud->fieldType('Monto_cobro','integer');
        $crud->fieldType('N_factura','integer');
        $crud->fieldType('N_reciboc','hidden');
        $crud->fieldType('Lugar_reciboc','dropdown', array("CANDY BAR"=>"CANDY BAR", "BOLETERIA"=>"BOLETERIA", "SABOR PERU"=>"SABOR PERU", "CAFE ITALIA"=>"CAFE ITALIA"));       
        $crud->fieldType('TarjetaID','hidden');       

       $crud->setRule('Fecha_cobro', 'Fecha de Cobro', 'trim|max_length[255]|required',["required"=>"Fecha de Cobroes Campo Obligatorio"]);
       $crud->setRule('Hora_cobro', 'Hora de Cobro', 'trim|max_length[255]|required',["required"=>"Hora de Cobro es Campo Obligatorio"]);
       $crud->setRule('Monto_Cobro', 'Monto de Cobro', 'trim|max_length[255]|required|integer|max['.$tarjeta->saldo.']',["required"=>"Monto de Cobro es Campo Obligatorio", "integer"=>"Monto de Recarga debe ser un Numero"]);
       $crud->setRule('N_factura', 'Numero Factura', 'trim|max_length[255]|required', ["required"=>"Numero Factura es Campo Obligatorio"]);       
       $crud->setRule('Lugar_reciboc', 'Fecha Nacimiento', 'required', ["required"=>"Lugar de Recibo es Campo Obligatorio"]);
       
       //$crud->setRule('Monto_cobro', 'cobro', 'max', $tarjeta->saldo);
       $crud->callbackBeforeInsert(function ($stateParameters) {
            $builder = $this->db->table('tarjeta');            
            $builder->set('Saldo', 'Saldo - '.$stateParameters->data['Monto_cobro'], false);
            $builder->where('TarjetaID', $stateParameters->data['TarjetaID']);
            $builder->update();
            $builder2 = $this->db->table('cobrartarjeta');
            $builder2->select('N_reciboc');
            $builder2->orderBy('CobrartarjetaID', 'DESC');
            $builder2->limit(1);
            $data = $builder2->get()->getRow('N_reciboc');
            $stateParameters->data['N_reciboc']=$data +1;
            
            return $stateParameters;
        });        

       $crud->requiredFields(['Fecha_cobro', 'Hora_cobro', 'Monto_cobro', 'N_facturac', 'Lugar_reciboc']);
  
       $crud->where("cobrartarjeta.TarjetaID = $id_tarjeta");
       $crud->setActionButton('Imprimir Boleta', 'fa fa-money-bill-alt', function ($row) {
            return base_url().'/imprimirCobro/' . $row;
        }, true);
        
       $output = $crud->render();    
       $data['tarjeta'] = $tarjeta;        
       $data['output'] = $output;
       return view('cobrar', $data);    
    }

    public function anularTarjeta($id_tarjeta){
        echo $id_tarjeta;
    }    

    public function imprimirRecarga($id_registro){
        $session = session();
        $builder = $this->db->table('cargartarjeta');
        $builder->where('CargartarjetaID', $id_registro);
        $cargartarjeta= $builder->get()->getRow();
        $CargartarjetaID = $cargartarjeta->CargartarjetaID;
        $Fecha_carga = $cargartarjeta->Fecha_carga;
        $Hora_carga = $cargartarjeta->Hora_carga;
        $Monto_recarga = $cargartarjeta->Monto_recarga;
        $Porcentaje_beneficiario = $cargartarjeta->Porcentaje_beneficiario;
        $N_recibor = $cargartarjeta->N_recibor;
        $Lugar_recibor = $cargartarjeta->Lugar_recibor;
        $TarjetaID =$cargartarjeta->TarjetaID;
        $builder = $this->db->table("tarjeta");
        $builder->where('TarjetaID', $TarjetaID);
        $tarjeta= $builder->get()->getRow();
        $Codigo_tarjeta =$tarjeta->Codigo_tarjeta;
        $Fechacaducidad = $tarjeta->Fechacaducidad;
        $Saldo = $tarjeta->Saldo;
        $ClienteID = $tarjeta->ClienteID;
        $builder = $this->db->table("cliente");
        $builder->where('id', $ClienteID);
        $cliente= $builder->get()->getRow();
        $Nombre = $cliente->Nombre;
        $Apellido = $cliente->Apellido;
        $Ci_nit = $cliente->Ci_nit;
        $data['id_tarjeta'] = $id_registro;
        $data['codigo_tarjeta'] = $Codigo_tarjeta;
        $data['fecha'] = $Fecha_carga;
        $data['hora'] = $Hora_carga;
        $data['nombre_cliente'] = $Nombre." ".$Apellido;
        $data['ci'] = $Ci_nit;
        $data['lugar'] = $Lugar_recibor;
        $data['monto'] = $Monto_recarga;
        $data['porcentaje'] = $Porcentaje_beneficiario;
        $data['monto_total'] = $Monto_recarga + $Monto_recarga*($Porcentaje_beneficiario/100.0);        
        $data['usuario'] = $session->get('Cuenta');
        return view('reciboCarga', $data);
    }

    public function imprimirCobro($id_registro){
        $session = session();
        $builder = $this->db->table('cobrartarjeta');
        $builder->where('CobrartarjetaID', $id_registro);
        $cobrartarjeta= $builder->get()->getRow();        
        $Fecha_cobro = $cobrartarjeta->Fecha_cobro;
        $Hora_cobro = $cobrartarjeta->Hora_cobro;
        $Monto_cobro = $cobrartarjeta->Monto_cobro;        
        $N_factura = $cobrartarjeta->N_factura;
        $N_reciboc = $cobrartarjeta->N_reciboc;
        $Lugar_reciboc = $cobrartarjeta->Lugar_reciboc;
        $TarjetaID =$cobrartarjeta->TarjetaID;
        $builder = $this->db->table("tarjeta");
        $builder->where('TarjetaID', $TarjetaID);
        $tarjeta= $builder->get()->getRow();
        $Codigo_tarjeta =$tarjeta->Codigo_tarjeta;
        $Fechacaducidad = $tarjeta->Fechacaducidad;
        $Saldo = $tarjeta->Saldo;
        $ClienteID = $tarjeta->ClienteID;
        $builder = $this->db->table("cliente");
        $builder->where('id', $ClienteID);
        $cliente= $builder->get()->getRow();
        $Nombre = $cliente->Nombre;
        $Apellido = $cliente->Apellido;
        $Ci_nit = $cliente->Ci_nit;
        $data['id_tarjeta'] = $N_reciboc;
        $data['codigo_tarjeta'] = $Codigo_tarjeta;
        $data['saldo'] = $Saldo;
        $data['fecha_caducidad'] = $Fechacaducidad;
        $data['fecha'] = $Fecha_cobro;
        $data['hora'] = $Hora_cobro;
        $data['nombre_cliente'] = $Nombre." ".$Apellido;
        $data['ci'] = $Ci_nit;
        $data['lugar'] = $Lugar_reciboc;
        $data['monto'] = $Monto_cobro;
        $data['factura'] = $N_factura;        
        $data['usuario'] = $session->get('Cuenta');
        return view('reciboCobro', $data);
    }
}
