// categoria controller
const categoria_routes = {
  getAll: 'api/v2/categorias',
  getByCliente: `api/v2/categorias/search_all/cliente`,
  getByPrenda: `api/v2/categorias/search_all/prenda`,
  get: (id) => `api/v2/categoria/${id}`,
  create: 'api/v2/categoria/create',
  update: 'api/v2/categoria/update',
  delete: 'api/v2/categoria/delete'
}

class Categoria extends Corex {
  async create(data) {
    return this.request(categoria_routes.create, { method: 'POST', body: data });
  }
  async update(data) {
    return this.request(categoria_routes.update, { method: 'POST', body: data });
  }
  async delete(id) {
    const formData = new FormData();
    formData.append('id', id);
    return this.request(categoria_routes.delete, { method: 'POST', body: formData });
  }
  async getById(id) {
    return this.request(categoria_routes.get(id));
  }
  async getAll(filters = {}) {
    const queryString = new URLSearchParams(filters).toString();
    const url = queryString 
        ? `${categoria_routes.getAll}?${queryString}`
        : categoria_routes.getAll;
    return this.request(url);
    // return this.request(categoria_routes.getAll, { method: 'POST', body: filters });
  }
  async getByCliente(filters) {
    return this.request(categoria_routes.getByCliente, { method: 'POST', body: filters });
  }
}

const categoria_api = new Categoria();
window.categoria_api = categoria_api;