FROM nginx AS jusbrasil_nginx

COPY conf/nginx.conf /etc/nginx/nginx.conf

ADD sites-enabled/vhost_dev.conf /etc/nginx/sites-enabled/vhost_dev.conf

CMD ["nginx", "-g", "daemon off;"]
