DROP DATABASE IF EXISTS eew;
CREATE DATABASE eew DEFAULT CHARACTER SET utf8;
CREATE TABLE eew.logs(
    time INT NOT NULL PRIMARY KEY,
    available BOOLEAN NOT NULL DEFAULT TRUE,
    region VARCHAR(255),
    latitude FLOAT,
    longitude FLOAT,
    depth INT,
    japanese_intensity INT,
    magunitude FLOAT,
    report_num INT,
    alert_type VARCHAR(15),
    final BOOLEAN,
    origin_time INT
);