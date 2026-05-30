let routes_sesion = {
  login: `api/v1/auth/login`,
  logout: `api/v1/auth/logout`,
  set_sede: `api/v1/session/set_sede`,
  profile_update: 'api/v1/profile/update'
}

class Sesion extends Corex {
  async login(data) {
    return this.request(routes_sesion.login, { method: 'POST', body: data });
  }
  async logout(data) {
    return this.request(routes_sesion.logout, { method: 'POST', body: data });
  }
  async set_sede(id) {
    const formData = new FormData();
    formData.append('sede_id', id);
    return this.request(routes_sesion.set_sede, { method: 'POST', body: formData });
  }
  async profile_update(data) {
    return this.request(routes_sesion.profile_update, { method: 'POST', body: data });
  }
}

const sesion_api = new Sesion();
window.sesion_api = sesion_api;