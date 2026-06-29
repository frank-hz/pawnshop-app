// Credito controller
const credito_routes = {
  getAll: 'api/v2/creditos/search_all',
  getByCliente: `api/v2/creditos/search_all/cliente`,
  getByPrenda: `api/v2/creditos/search_all/prenda`,
  get: (id) => `api/v2/credito/${id}`,
  create: 'api/v2/credito/create',
  update: 'api/v2/credito/update',
  delete: 'api/v2/credito/delete'
}

class Credito extends Corex {
  async create(data) {
    return this.request(credito_routes.create, { method: 'POST', body: data });
  }
  async update(data) {
    return this.request(credito_routes.update, { method: 'POST', body: data });
  }
  async delete(id) {
    const formData = new FormData();
    formData.append('id', id);
    return this.request(credito_routes.delete, { method: 'POST', body: formData });
  }
  async getById(id) {
    return this.request(credito_routes.get(id));
  }
  async getAll(filters) {
    return this.request(credito_routes.getAll, { method: 'POST', body: filters });
  }
  async getByCliente(filters) {
    return this.request(credito_routes.getByCliente, { method: 'POST', body: filters });
  }
}

const credito_api = new Credito();
window.credito_api = credito_api;