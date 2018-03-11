# swoole_fuelphp

swoole使用fuelphp框架

### 環境

php 7.2.3

swoole 2.1.1

fuelphp 1.7.1

### Nginx

```nginx
server {
    root /var/www/html/public;
    listen 8080 default_server;
    server_name _;

    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        if (!-e $request_filename) {
             proxy_pass http://127.0.0.1:8888;
        }
    }

    location ^~ /assets {
        alias /var/www/html/public/assets;
        try_files $uri $uri/ 404;
    }
}
```

### Docker
PHP7.2.3 + Swoole 2.1.1
