class API {
  static async add(endpointUrl, data, setErrorMsg) {
    return await this.send(endpointUrl, data, 'POST', setErrorMsg);
  }

  static async delete(endpointUrl, setErrorMsg) {
    return await this.send(endpointUrl, {}, 'DELETE', setErrorMsg);
  }

  static async edit(endpointUrl, data, setErrorMsg) {
    return await this.send(endpointUrl, data, 'PATCH', setErrorMsg);
  }

  static async send(endpointUrl, data, method, setErrorMsg) {
    console.log(JSON.stringify(data));
    const fetchOptions = {
      method: method,
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data),
    };
    const response = await fetch(endpointUrl, fetchOptions);
    if (response.ok) {
      return true;
    }

    const responseJson = await response.json();
    setErrorMsg(responseJson.error);
    return false;
  }
}

export default API;
