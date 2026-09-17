let categoria_routes = {
  getAll: 'api/v2/categorias',
  get: (id) => `api/v2/categoria/${id}`,
  create: 'api/v2/categoria/create',
  update: 'api/v2/categoria/update',
  delete: 'api/v2/categoria/delete'
}
let tipo_prenda_routes = {
  getAll: 'api/v2/tipos_prenda',
  get: (id) => `api/v2/tipo_prenda/${id}`,
  create: 'api/v2/tipo_prenda/create',
  update: 'api/v2/tipo_prenda/update',
  delete: 'api/v2/tipo_prenda/delete'
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
  async getAll(filters = {}, opt = {}) {
    const queryString = new URLSearchParams(filters).toString();
    const options = (opt && opt.length > 0) ? `&${opt}` : '';
    const url = queryString 
      ? `${categoria_routes.getAll}?${queryString}${options}`
      : categoria_routes.getAll;
    return this.request(url);
  }

  async get_all_v2(filters = {}) {
    const params = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value === undefined || value === null || value === '') return;

      if (Array.isArray(value)) {
        value.forEach(v => params.append(`${key}[]`, v));
      } else {
        params.append(key, value);
      }
    });

    const queryString = params.toString();
    const url = queryString 
      ? `${categoria_routes.getAll}?${queryString}`
      : categoria_routes.getAll;
      console.log(url);
    return this.request(url);
}
}
class TipoPrenda extends Corex {
  async create(data) {
    return this.request(tipo_prenda_routes.create, { method: 'POST', body: data });
  }
  async update(data) {
    return this.request(tipo_prenda_routes.update, { method: 'POST', body: data });
  }
  async delete(id) {
    const formData = new FormData();
    formData.append('id', id);
    return this.request(tipo_prenda_routes.delete, { method: 'POST', body: formData });
  }
  async getById(id) {
    return this.request(tipo_prenda_routes.get(id));
  }
  async getAll(filters = {}) {
    const queryString = new URLSearchParams(filters).toString();
    const url = queryString 
        ? `${tipo_prenda_routes.getAll}?${queryString}`
        : tipo_prenda_routes.getAll;
    return this.request(tipo_prenda_routes.getAll);
  }
}

const categoria_api = new Categoria();
const tipo_prenda_api = new TipoPrenda();

window.categoria_api = categoria_api;
window.tipo_prenda_api = tipo_prenda_api;