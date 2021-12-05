CREATE TABLE logs(
    time INT(11) NOT NULL PRIMARY KEY,
    available BOOLEAN NOT NULL DEFAULT TRUE,
    region VARCHAR(255),
    latitude FLOAT(8, 6),
    longitude FLOAT(10, 6),
    depth INT(5),
    japanese_intensity INT(2),
    magunitude FLOAT(5, 2),
    report_num INT(4),
    alert_type VARCHAR(15),
    final BOOLEAN,
    origin_time INT(11),
);