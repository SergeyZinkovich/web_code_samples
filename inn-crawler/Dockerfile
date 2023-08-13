FROM python:3.10

RUN apt-get update && apt-get -y install \
    cron \
    nano

RUN mkdir -p /var/www/inn_parser

RUN touch /var/log/cron.log
RUN chmod 0644 /var/log/cron.log

COPY ./entrypoint.sh var/www/inn_parser/
RUN chmod +x var/www/inn_parser/entrypoint.sh
COPY ./src var/www/inn_parser/src
COPY ./resources var/www/inn_parser/resources
COPY ./app.py var/www/inn_parser/

WORKDIR /var/www/inn_parser

RUN python3 -m venv ./venv
RUN chmod +x ./venv/bin/activate

COPY /requirements.txt /
RUN ./venv/bin/activate && pip install -r /requirements.txt

RUN export FLASK_APP=app.py
RUN export FLASK_ENV=development
RUN flask crontab add

EXPOSE 5000

ENTRYPOINT ["./entrypoint.sh"]
