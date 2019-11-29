FROM debian:stable
RUN apt-get update
RUN apt-get install -y python3 python3-pip supervisor
RUN pip3 install --upgrade pip
RUN pip3 install supervisor
COPY . /opt/RedOpsAgent
WORKDIR /opt/RedOpsAgent
RUN mkdir -p /etc/supervisor/conf.d/ && mkdir -p /var/log/supervisor
RUN cp conf/redops_agent.conf /etc/supervisor/conf.d/supervisord.conf
RUN pip3 install -r requirements.txt
EXPOSE 8500 5000
CMD ["/usr/bin/supervisord"]
