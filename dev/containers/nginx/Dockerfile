FROM ghcr.io/nulldark/nginx:latest

COPY nginx.conf /etc/nginx/

RUN rm /etc/nginx/conf.d/default.conf

ADD ./startup.sh /opt/startup.sh
RUN sed -i 's/\r//g' /opt/startup.sh
CMD ["/bin/bash", "/opt/startup.sh"]

EXPOSE 80 443