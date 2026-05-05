<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$pagoCreate = Array(
    [
        'field' => 'credito_id',
        'label' => 'Credito',
        'rules' => 'required',
        'errors' => [
            'required' => 'El id del credito es requerido'
        ],
        'field' => 'empleado_id',
        'label' => 'Credito',
        'rules' => 'required',
        'errors' => [
            'required' => 'El id del empleado es requerido'
        ],
        'field' => 'forma_pago',
        'label' => 'Forma Pago',
        'rules' => 'required|',
        'errors' => [
            'required' => 'El id del credito es requerido'
        ],
    ]

);

$config = array(
    'cliente_register' => array(        
        [
            'field' => 'documento',
            'label' => 'Documento',
            'rules' => 'required|min_length[8]|max_length[20]|is_unique[cliente.cli_documento]',
            'errors' => array(
                'required' => 'Se requiere un %s.',
                'min_length' => '%s debe tener al menos 8 caracteres.',
                'max_length' => '%s no puede tener mas de 20 caracteres.',
                'is_unique' => 'el %s pertenece a un cliente existente.'
            )
        ],
        [
            'field' => 'tipo_documento',
            'label' => 'Tipo Documento',
            'rules' => 'required',
            'errors' => array(
                'required' => ' un %s.',
            )
        ],
        [
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere un %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'paterno',
            'label' => 'Ape. Paterno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere un %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'materno',
            'label' => 'Ape. Materno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere un %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'ubigeo',
            'label' => 'Ciudad/Distrito',
            'rules' => 'required|integer',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'integer' => '%s debe ser un valor valido.'
            )
        ],
        [
            'field' => 'telefono',
            'label' => 'Telefono',
            'rules' => 'required|max_length[50]',
            'errors' => array(
                'required' => 'Se requiere un %s.',
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'max_length[50]',
            'errors' => array(
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'direccion',
            'label' => 'Direccion',
            'rules' => 'max_length[100]',
            'errors' => array(
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ]
    ),
    'empleado_crear' => array(        
        [
            'field' => 'documento',
            'label' => 'Documento',
            'rules' => 'required|min_length[8]|max_length[20]|is_unique[empleado.emp_documento]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'min_length' => '%s debe tener al menos 8 caracteres.',
                'max_length' => '%s no puede tener mas de 20 caracteres.',
                'is_unique' => 'el %s pertenece a un empleado existente.'
            )
        ],
        [
            'field' => 'tipo_documento',
            'label' => 'Tipo Documento',
            'rules' => 'required',
            'errors' => array(
                'required' => ' un %s.',
            )
        ],
        [
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'paterno',
            'label' => 'Ape. Paterno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'materno',
            'label' => 'Ape. Materno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'ubigeo',
            'label' => 'Ciudad/Distrito',
            'rules' => 'required|callback_verificar_ubigeo',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'verificar_ubigeo' => 'Ciudad no valida.'
            )
        ],
        [
            'field' => 'telefono',
            'label' => 'Telefono',
            'rules' => 'required|max_length[50]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'max_length[50]',
            'errors' => array(
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'direccion',
            'label' => 'Direccion',
            'rules' => 'required|max_length[100]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'sede',
            'label' => 'Sede',
            'rules' => 'required',
            'errors' => array(
                'required' => '%s es requerido.',

            )
        ]
    ),
    'empleado_editar' => array(      
        [
            'field' => 'id',
            'label' => 'Empleado',
            'rules' => 'required|integer',
            'errors' => array(
                'required' => 'Empleado no identificado.',
                'integer' => 'Empleado invalido.',
            )
        ],  
        [
            'field' => 'documento',
            'label' => 'Documento',
            'rules' => 'required|min_length[8]|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'min_length' => '%s debe tener al menos 8 caracteres.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'tipo_documento',
            'label' => 'Tipo Documento',
            'rules' => 'required',
            'errors' => array(
                'required' => ' un %s.',
            )
        ],
        [
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'paterno',
            'label' => 'Ape. Paterno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'materno',
            'label' => 'Ape. Materno',
            'rules' => 'required|max_length[20]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 20 caracteres.'
            )
        ],
        [
            'field' => 'ubigeo',
            'label' => 'Ciudad/Distrito',
            'rules' => 'required|callback_verificar_ubigeo',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'verificar_ubigeo' => 'Ciudad no valida.'
            )
        ],
        [
            'field' => 'telefono',
            'label' => 'Telefono',
            'rules' => 'required|max_length[50]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'max_length[50]',
            'errors' => array(
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'direccion',
            'label' => 'Direccion',
            'rules' => 'required|max_length[100]',
            'errors' => array(
                'required' => 'Se requiere %s.',
                'max_length' => '%s no puede tener mas de 50 caracteres.'
            )
        ],
        [
            'field' => 'sede',
            'label' => 'Sede',
            'rules' => 'required|callback_verificar_sede',
            'errors' => array(
                'required' => '%s es requerido.',
                'verificar_sede' => 'Sede no valida.'

            )
        ]
    ),
    'pago_create' => $pagoCreate
);
