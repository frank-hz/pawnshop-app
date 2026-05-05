const APP_CONFIG = {
    PROTOCOL: 'http',
    HOST: 'localhost',
    NAME: 'sistema-dev',
    ENV: 'DEV'
}

export class Server {
    URL(bind_value = ''){
        let host = APP_CONFIG.HOST;
        let name = APP_CONFIG.NAME;
        let protocol = APP_CONFIG.PROTOCOL;
        return `${protocol}://${host}/${name}/${bind_value}`;
    }
}