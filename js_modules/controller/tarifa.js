let tarifa_sesion = {
  cotizar: `api/v1/auth/login`,
}

class Tarifa extends Corex {
//   async login(data) {
//     return this.request(routes_sesion.login, { method: 'POST', body: data });
//   }
//   async logout(data) {
//     return this.request(routes_sesion.logout, { method: 'POST', body: data });
//   }
//   async set_sede(id) {
//     const formData = new FormData();
//     formData.append('sede_id', id);
//     return this.request(routes_sesion.set_sede, { method: 'POST', body: formData });
//   }
  async cotizar(data) {
    return this.request(tarifa_sesion.cotizar, { method: 'POST', body: data });
  }
}

const tarifa_api = new Sesion();
window.tarifa_api = tarifa_api;