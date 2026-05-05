(function(){
    let app;
    document.addEventListener('DOMContentLoaded', ()=>{
        app = new App();
        try {            
            let detalle = new Vue({
                el: '#credito_detalle',
                data: {
                    id: 0,
                    cliente: 0,
                    nota_dataset: [],
                    alerta_dataset: [],
                    notas: [],
                    cuotas: [],
                    pagos: [],
                    nota_log: '',
                    cuota_log: '',
                    atraso_data: '--',
                    nota_detail: {
                        id: 0,
                        descripcion: '',
                        anclado: 'NO'
                    }               
                },
                created: function(){
                    let urlx = window.location.href.split('/');
                    let idx = urlx[urlx.length - 1];
                    if (idx && parseInt(idx) > 0) {
                        this.id = idx;
                    }
                    this.cliente = document.getElementById('pv_cliente').value;
                    this.AlertaListado();
                    this.CuotasAtraso();
                    this.NotaListado();
                    this.buscar_cuotas();
                    this.buscar_pagos();
                },
                methods: {
                    async EliminarCredito(event){
                        try {
                            if (event.target.dataset.id) {
                                if (confirm('Eliminar credito?')) {
                                    app.preload('open', 'Procesando..');
                                    let credito = new Credito();
                                    let elimina = await credito.Eliminar(event.target.dataset.id);

                                    app.preload('close');
                                    if (elimina.status === 'OK') {
                                        not.Ok(elimina.mssg, '', 3000);
                                        setTimeout(() => {
                                            window.location.href = srv.url()+'Credito';
                                        }, 500);
                                    }else{
                                        if (elimina.status === 'WARN') 
                                            not.Aviso(elimina.mssg, '', 3000);
                                        else
                                            not.Error(elimina.mssg, '', 3000);
                                    }
                                }
                            }else {
                                console.log('no code');
                            }
                        } catch (error) {
                            not.Error('Error al procesar', '', 3000);
                        }
                    },
                    async AlertaListado(){
                        try {
                            this.alerta_dataset = [];
                            let _data = new FormData();
                            _data.append('id', this.cliente);
                            let request = await fetch(srv.url()+'AlertaCliente/lista', {method: 'POST', body: _data});
                            let result = await request.json();
                            this.alerta_dataset = result;
                        } catch (error) {
                            console.log('Alertas::'+error.message);
                        }
                    },
                    async AlertaFinalizar(event){
                        try {
                            if (event.target.dataset.id && event.target.dataset.estado) {
                                if (confirm('¿Finalizar alerta pendiente?')) {
                                    let _data = new FormData();
                                    _data.append('id', parseInt(event.target.dataset.id));
                                    _data.append('estado', (event.target.dataset.estado === 'P') ? 'F' : 'P');
                                    
                                    let send = await fetch(srv.url()+'AlertaCliente/modificar', {method: 'POST', body: _data});
                                    let result = await send.json();
                                    if (result.status === 'OK') {
                                        not.Ok('Alerta', 'Finalizacion aplicada', 2000);
                                        this.AlertaListado();
                                    }else{
                                        not.Aviso('Alerta', 'Proceso no completado', 2000);
                                    }
                                }
                            }
                        } catch (error) {
                            not.Error('Alerta', 'Error al procesar', 2000);
                        }
                    },
                    async CuotasAtraso(){
                        try {
                            let data = new FormData();
                            data.append('tipo', 'atraso');
                            data.append('buscar', this.id);
                            let request = await fetch(srv.url()+'Credito/serv_rest', {method: 'POST', body: data})
                            let result = await request.json();
                            if (result.status === 'OK') {
                                this.atraso_data = result.atraso;
                            }else{
                                console.log(result.message);
                            }
                        } catch (error) {
                            
                        }
                    },
                    async modificar_nota(event){
                        try {
                            if (event.target.dataset.id && event.target.dataset.editar) {
                                if (confirm('¿Modificar Nota?')) {
                                    let _data = new FormData();
                                    _data.append('id', event.target.dataset.id);
                                    _data.append('anclado', event.target.dataset.editar);
                                    fetch(srv.url()+'NotaCredito/actualizar_anclaje', {method: 'POST', body: _data})
                                    .then(result => { return result.json() })
                                    .then(resp => {
                                        if (resp.status === 'OK') {
                                            app.renderAlertFree('nota_log', 'ok', resp.message, 1800);
                                        }else{
                                            app.renderAlertFree('nota_log', 'aviso', resp.message, 1800);
                                        }
                                        this.buscar_notas();
                                    })
                                }
                            }
                        } catch (error) {
                            console.log(error)
                        }
                    },
                    async NotaListado(){
                        this.nota_dataset = []; 
                        let _data = new FormData();
                        _data.append('id', this.id);
                        fetch(srv.url()+'NotaCredito/lista', {method: 'POST', body: _data})
                        .then(resp => { return resp.json(); })
                        .then(result => {
                            if (result.length > 0) {
                                result.forEach(element => {
                                    this.nota_dataset.push({
                                        id: element.id,
                                        descripcion: element.descripcion,
                                        fecha: element.fecha,
                                        anclado: element.anclado,
                                        row_css: (element.anclado === 'SI') ? 'bg-warning' : ''
                                    });
                                })
                            }
                        })
                        .catch(err => {
                            not.Error('Notas: ', 'Error al obtener notas', 2000);
                            console.log(err);
                        })
                    },
                    NotaNuevo(){
                        if (this.id) {
                            this.nota_detail.id = 0;
                            this.nota_detail.descripcion = '';
                            this.nota_dataset.anclado = 'NO';
                            $('#MdNotaNuevo').modal('show');
                        }else{
                            not.Aviso('Nueva Nota', 'Accion no permitida', 2000);
                        }
                    },
                    async NotaEditar(event){
                        if (event.target.dataset.id) {
                            let _data = new FormData();
                            _data.append('id', parseInt(event.target.dataset.id));
                            let request = await fetch(srv.url()+'NotaCredito/detalle', {
                                method: 'POST',
                                body : _data
                            })
                            let result = await request.json();
                            if (result) {
                                this.nota_detail.id = parseInt(event.target.dataset.id);
                                this.nota_detail.descripcion = result.descripcion;
                                this.nota_detail.anclado = result.anclado;
                                $('#MdNotaEditar').modal('show');
                            }
                        }
                    },
                    async NotaEliminar(){
                        if (event.target.dataset.id) {
                            if (confirm('¿Eliminar Nota?')) {
                                let nota = new NotaCredito();
                                let elimina = await nota.Eliminar(event.target.dataset.id);
                                if (elimina.status === 'OK') {
                                    not.Ok('Eliminar Nota', 'Completado con exito!', 2000);
                                }else{
                                    not.Aviso('Eliminar Nota', 'Accion no completada', 2000);
                                }
                                this.NotaListado();
                            }
                        }
                    },
                    async NotaActualizar(){
                        try {                    
                            let _data = new FormData();
                            _data.append('id', this.nota_detail.id);
                            _data.append('descripcion', this.nota_detail.descripcion);
                            _data.append('anclado', this.nota_detail.anclado);
                            let sending = await fetch(srv.url()+'NotaCredito/modificar', {
                                method: 'POST',
                                body: _data
                            })
                            let result = await sending.json();
                            if (result.status === 'OK') {
                                not.Ok('Editar Nota: ', 'result.message', 2000);
                                this.NotaListado();
                                $('#MdNotaEditar').modal('hide');
                            }else{
                                not.Aviso('Editar Nota: ', 'Accion no completada', 2000);
                            }
                        } catch (error) {
                            not.Error('Editar Nota: ', 'Error al procesar', 2000);
                        }
                    },
                    async NotaRegistrar(){
                        try {
                            let _data = new FormData();
                            _data.append('credito', this.id);
                            _data.append('descripcion', this.nota_detail.descripcion);
                            _data.append('anclado', this.nota_detail.anclado);
                            let sending = await fetch(srv.url()+'NotaCredito/registro', {method: 'POST', body: _data});
                            let result = await sending.json();
                            if (result.status === 'OK') {
                                this.nota_detail.descripcion = '';
                                this.nota_detail.anclado = 'NO';
                                this.NotaListado();
                                $('#MdNotaNuevo').modal('hide');
                                not.Ok('Nueva Nota', 'Registro completo!', 2000);
                            }else{
                                not.Aviso('Nueva Nota', result.message, 2000);
                            }
                        } catch (error) {
                            nota.Error('Nueva Nota', 'Error al procesar', 2000);
                        }
                    },
                    async buscar_cuotas(){
                        try {
                            this.cuotas = [];
                            this.cuota_log = '<small class="text-primary d-inline"><i class="fas fa-spinner fa-spin"></i></small>';
                            let credito = new Credito();
                            const consulta = await credito.ObtenerCuotas(this.id);                            
                            if(consulta && consulta.status === 'OK'){
                                this.cuotas = consulta.result;
                            }
                            this.cuota_log = '';
                        } catch (error) {
                            this.cuota_log = '<small class="d-inline text-danger">[Error al procesar]</small>';
                            setTimeout(() => {
                                this.cuota_log = '';    
                            }, 3000);
                            console.log(error);
                        }
                    },
                    async buscar_pagos(){
                        try {
                            this.pagos = [];                            
                            let credito = new Credito();
                            let consulta = await credito.ObtenerPagos(this.id);
                            if (consulta.status === 'OK' && consulta.result.length > 0) {
                                consulta.result.forEach(element => {
                                    let _concepto = '';
                                    switch (element.concepto) {
                                        case 'REG':
                                            _concepto = 'Regularizacion';
                                            break;
                                        case 'REC':
                                            _concepto = 'Recuperacion';
                                            break;
                                        case 'AMOR':
                                            _concepto = 'Amortizacion';
                                            break;
                                        case 'ADEL':
                                            _concepto = 'Adelanto';
                                            break;
                                        case 'ADEL_AUTO':
                                            _concepto = 'Adelanto automatico';
                                            break;
                                        case 'DEB_AUTO':
                                            _concepto = 'Debitacion automatica';
                                            break;
                                    }
                                    this.pagos.push({
                                        id: element.id,
                                        serie: element.serie,
                                        fecha: element.fecha,
                                        forma: element.forma,
                                        concepto: _concepto,
                                        total: element.total,
                                        url: srv.url()+'Pago/detalle/'+element.id
                                    });
                                })
                            }
                        } catch (error) {
                            console.log(error)
                        }
                    },
                    async congelar_credito(event){
                        try {
                            if (event.target.dataset.id) {
                                const aswer = Swal.fire({
                                    imageUrl: srv.url()+'assets/images/app_icon/pause.svg',
                                    imageWidth: 200,
                                    imageHeight: 100,
                                    html: '¿<b>Congelar</b> credito?',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3fd0d4',
                                    confirmButtonText: `Si, congelar` ,
                                    cancelButtonColor: '#283845',
                                    cancelButtonText: 'Cancelar'
                                }).then(async(result) => {
                                    if (result.value) {
                                        try {
                                            app.preload('open', 'Procesando..');
                                            let credito = new Credito();
                                            let congela = await credito.Congelar(event.target.dataset.id);
                                            app.preload('close');
                                            if (congela.status === 'OK') {
                                                not.Ok(congela.mssg, '', 2000);
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 350);
                                            }else{
                                                if (congela.status === 'WARN') 
                                                    not.Aviso(congela.mssg, '', 2200);
                                                else
                                                    not.Error(congela.mssg, '', 2200);
                                            }
                                        } catch (error) {
                                            not.Error('Error al procesar', '', 2200);
                                        }
                                    }
                                });
                            }
                        } catch (error) {
                            
                        }
                    },
                    async rematar_credito(event){
                        try {
                            if (event.target.dataset.id) {
                                const aswer = Swal.fire({
                                    imageUrl: srv.url()+'assets/images/app_icon/shop.svg',
                                    imageWidth: 200,
                                    imageHeight: 100,
                                    html: '¿<b>Rematar</b> credito?',
                                    showCancelButton: true,
                                    confirmButtonColor: '#0070E8',
                                    confirmButtonText: `Si, rematar` ,
                                    cancelButtonColor: '#283845',
                                    cancelButtonText: 'Cancelar'
                                }).then(async(result) => {
                                    if (result.value) {
                                        try {
                                            app.preload('open', 'Procesando..');
                                            let credito = new Credito();
                                            let remata = await credito.Remate(event.target.dataset.id);
                                            app.preload('close');
                                            if (remata.status === 'OK') {
                                                not.Ok(remata.mssg, '', 3000);
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 350);
                                            }else{
                                                if (remata.status === 'WARN') 
                                                    not.Aviso(remata.mssg, '', 3000);
                                                else
                                                    not.Error(remata.mssg, '', 3000);
                                            }
                                        } catch (error) {
                                            not.Error('Error al procesar', '', 3000);
                                        }
                                    }
                                });
                            }
                        } catch (error) {
                            
                        }
                    },
                    async modificar_cobro_mora(event){
                        if (event.target.dataset.id && event.target.dataset.estado) {
                            let tipo = (event.target.dataset.estado === 'A') ? 'ACTIVAR' : 'DESACTIVAR';
                            const aswer = Swal.fire({
                                type: 'warning',
                                html: `<b>${tipo}</b> la aplicacion de penalidad?`,
                                showCancelButton: true,
                                confirmButtonColor: '#00B388',
                                confirmButtonText: `Si, ${tipo}` ,
                                cancelButtonColor: '#283845',
                                cancelButtonText: 'Cancelar'
                            }).then(async(result) => {
                                if (result.value) {
                                    try {
                                        let credito = new Credito();
                                        let modificar = await credito.ModificaPenalidad(event.target.dataset.id, event.target.dataset.estado);
                                        if (modificar.status === 'OK') {
                                            not.Ok(modificar.mssg, '', 3000);
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 400);
                                        }else{
                                            if (modificar.status === 'WARN') 
                                                not.Aviso(modificar.mssg, '', 3000);
                                            else
                                                not.Error(modificar.mssg, '', 3000);
                                        }
                                    } catch (error) {
                                        not.Error('Error al procesar', '', 3000);
                                    }
                                }
                            });
                        }
                    }                    
                }
            });
        } catch (error) {
            console.log(error);
        }

    })
})();