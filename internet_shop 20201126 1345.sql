--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 7.2.58.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 26.11.2020 13:45:43
-- Версия сервера: 5.5.5-10.3.22-MariaDB
-- Версия клиента: 4.1
--


-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установить режим SQL (SQL mode)
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

-- 
-- Установка базы данных по умолчанию
--
USE internet_shop;

--
-- Описание для таблицы discounts
--
DROP TABLE IF EXISTS discounts;
CREATE TABLE IF NOT EXISTS discounts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) DEFAULT '''NULL''' COMMENT 'Заголовок',
  description TEXT DEFAULT NULL COMMENT 'Полное описание условий',
  discount INT(11) DEFAULT 0 COMMENT 'Скидка в процентах',
  dt_from DATE DEFAULT 'NULL',
  dt_to DATE DEFAULT 'NULL',
  tm_from TIME DEFAULT 'NULL',
  tm_to TIME DEFAULT 'NULL',
  day1 INT(11) DEFAULT 0 COMMENT 'Понедельник',
  day2 INT(11) DEFAULT 0 COMMENT 'Вторник',
  day3 INT(11) DEFAULT 0 COMMENT 'Среда',
  day4 INT(11) DEFAULT 0 COMMENT 'Четверг',
  day5 INT(11) DEFAULT 0 COMMENT 'Пятница',
  day6 INT(11) DEFAULT 0 COMMENT 'Суббота',
  day7 INT(11) DEFAULT 0 COMMENT 'Воскресение',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 16384
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
COMMENT = 'Таблица скидок'
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы feedbacks
--
DROP TABLE IF EXISTS feedbacks;
CREATE TABLE IF NOT EXISTS feedbacks (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  author VARCHAR(255) NOT NULL COMMENT 'автор',
  text TEXT NOT NULL COMMENT 'текст отзыва',
  create_date TIMESTAMP NOT NULL DEFAULT 'current_timestamp()' COMMENT 'дата создания',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы goods
--
DROP TABLE IF EXISTS goods;
CREATE TABLE IF NOT EXISTS goods (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description_short TEXT DEFAULT NULL,
  thumbnail VARCHAR(255) DEFAULT 'NULL',
  property TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  price DOUBLE DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы order_status
--
DROP TABLE IF EXISTS order_status;
CREATE TABLE IF NOT EXISTS order_status (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255) DEFAULT 'NULL',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 7
AVG_ROW_LENGTH = 2730
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы pictures
--
DROP TABLE IF EXISTS pictures;
CREATE TABLE IF NOT EXISTS pictures (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  path VARCHAR(50) NOT NULL,
  name VARCHAR(50) NOT NULL,
  size INT(11) UNSIGNED NOT NULL,
  click INT(11) UNSIGNED NOT NULL DEFAULT 0,
  alt VARCHAR(255) DEFAULT 'NULL',
  product_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Код продукта, куда относится картинка',
  thumbnail INT(11) DEFAULT 0 COMMENT 'Признак, что это главная картинка',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 11
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы users
--
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  login VARCHAR(255) NOT NULL,
  password VARCHAR(255) DEFAULT 'NULL',
  fio VARCHAR(255) DEFAULT 'NULL',
  is_admin INT(11) DEFAULT 0,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 8
AVG_ROW_LENGTH = 2340
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы cart
--
DROP TABLE IF EXISTS cart;
CREATE TABLE IF NOT EXISTS cart (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'просто ид',
  goods_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'код товара',
  user_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'код пользователя',
  quantity INT(11) UNSIGNED DEFAULT 1 COMMENT 'Количество',
  PRIMARY KEY (id),
  CONSTRAINT FK_cart_goods_id FOREIGN KEY (goods_id)
    REFERENCES goods(id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_cart_user_id2 FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 28
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы orders
--
DROP TABLE IF EXISTS orders;
CREATE TABLE IF NOT EXISTS orders (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  vsego DOUBLE DEFAULT NULL COMMENT 'Общая стоимость по заказу',
  order_date DATETIME DEFAULT 'NULL' COMMENT 'Дата и время создания заказа',
  user_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Код пользователя-заказчика',
  status_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Код состояния заказа',
  PRIMARY KEY (id),
  CONSTRAINT FK_orders_status_id FOREIGN KEY (status_id)
    REFERENCES order_status(id) ON DELETE NO ACTION ON UPDATE RESTRICT,
  CONSTRAINT FK_orders_user_id FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE NO ACTION ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 8
AVG_ROW_LENGTH = 3276
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

--
-- Описание для таблицы orders_detail
--
DROP TABLE IF EXISTS orders_detail;
CREATE TABLE IF NOT EXISTS orders_detail (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT(11) UNSIGNED DEFAULT NULL,
  goods_id INT(11) UNSIGNED DEFAULT NULL,
  quantity SMALLINT(6) DEFAULT NULL,
  price DOUBLE DEFAULT NULL COMMENT 'Базовая стоимость единицы товара',
  discount DOUBLE DEFAULT NULL COMMENT 'Размер скидки',
  itogo DOUBLE DEFAULT NULL COMMENT 'Стоимость единицы товара со скидкой',
  vsego DOUBLE DEFAULT NULL COMMENT 'Произведение суммы со скидкой на количество',
  discount_message VARCHAR(255) DEFAULT 'NULL',
  PRIMARY KEY (id),
  CONSTRAINT FK_orders_detail_goods_id FOREIGN KEY (goods_id)
    REFERENCES goods(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT FK_orders_detail_order_id FOREIGN KEY (order_id)
    REFERENCES orders(id) ON DELETE CASCADE ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 1820
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

-- 
-- Вывод данных для таблицы discounts
--

/*!40000 ALTER TABLE discounts DISABLE KEYS */;
INSERT INTO discounts VALUES
(1, 'Скидка каждую пятницу!', NULL, 5, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 1, 0, 0);

/*!40000 ALTER TABLE discounts ENABLE KEYS */;

-- 
-- Вывод данных для таблицы feedbacks
--

/*!40000 ALTER TABLE feedbacks DISABLE KEYS */;
INSERT INTO feedbacks VALUES
(1, 'Василий', 'Все круто!', '2020-11-20 22:02:57'),
(3, 'Дмитрий', 'Спасибо!', '2020-11-20 22:03:10');

/*!40000 ALTER TABLE feedbacks ENABLE KEYS */;

-- 
-- Вывод данных для таблицы goods
--

/*!40000 ALTER TABLE goods DISABLE KEYS */;
INSERT INTO goods VALUES
(1, '10" Ноутбук 4Good Cl100 синий', 'Ноутбук 4Good Cl100 станет для вас великолепным выбором, если вам необходим мобильный компьютер, обладающий предельно компактными размерами. Корпус модели характеризуется габаритами 185×280×28 мм. Невысокая масса ноутбука (она равна лишь 1056 г) позволит вам постоянно носить устройство с собой, не испытывая неудобств. Ноутбук поместится даже в папку с документами, которую, в свою очередь, вы сможете носить в небольшом портфеле. 10-дюймовый экран устройства имеет разрешение 1024×600.', '5fb27672.jpg', '<table class="tbl_harakteristiki">\r\n            <tr>\r\n                <td>Операционная система</td>\r\n                <td>Windows 10</td>\r\n            </tr>\r\n            <tr>\r\n                <td>Диагональ экрана</td>\r\n                <td>10&quot;</td>\r\n            </tr>\r\n            <tr>\r\n                <td>Процессор</td>\r\n                <td>Intel Atom</td>\r\n            </tr>\r\n            <tr>\r\n                <td>Размер оперативной памяти</td>\r\n                <td>2 ГБ</td>\r\n            </tr>\r\n            <tr>\r\n                <td>Жесткий диск</td>\r\n                <td>(SSD) 32 Гб</td>\r\n            </tr>\r\n        </table>', 'Ноутбук 4Good Cl100 работает под управлением удобной в использовании операционной системы Windows 10. Вы будете обеспечены широкой совместимостью мобильного компьютера с различным периферийным оборудованием: сканерами, принтерами, многофункциональными устройствами, веб-камерами и другой техникой. Аппаратную основу ноутбука составляют 4-ядерный процессор Atom Z3735F, 2 ГБ оперативной памяти DDR3L и встроенный видеоадаптер Intel HD. Для размещения данных используется скоростной 32-гигабайтный SSD-накопитель. Ноутбук 4Good Cl100 оборудован модулем Wi-Fi и Bluetooth-интерфейсом. В наличии 2 порта USB 2.0. Для вывода изображения используется порт HDMI, подходящий для подключения к телевизорам.', 7000),
(2, '11.6" Ноутбук Irbis NB31 белый', 'Заниматься веб-серфингом, просматривать медиаконтент, играть и общаться с помощью ноутбука Irbis NB31 — одно удовольствие. Диагональ его экрана составляет 11.6″, разрешение 1366×768. Встроенная видеокарта Intel HD. 4-х ядерный процессор, а оперативная память типа DDR3 на 2 ГБ. Накопитель SSD — 32 ГБ.Два порта USB 2.0, встроенная веб-камера. Время автономной работы — 3 ч 30 мин.', '5fb27679.jpg', '<table class="tbl_harakteristiki">\r\n                <tbody><tr>\r\n                    <td>Операционная система</td>\r\n                    <td>Windows 10</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Диагональ экрана</td>\r\n                    <td>11.6"</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Процессор</td>\r\n                    <td>Intel Atom</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Размер оперативной памяти</td>\r\n                    <td>2 ГБ</td>\r\n                </tr>\r\n                <tr>\r\n                    <td>Жесткий диск</td>\r\n                    <td>(SSD) 32 Гб</td>\r\n                </tr>\r\n            </tbody></table>', 'Заниматься веб-серфингом, просматривать медиаконтент, играть и общаться с помощью ноутбука Irbis NB31 — одно удовольствие. Это качественное и практичное решение, диагональ его экрана составляет 11.6″, глянцевое покрытие добавит краскам сочности и яркости, а с разрешением 1366×768 просмотр станет еще более комфортным. О хорошей графике позаботится встроенная видеокарта Intel HD. За быстродействие и хороший разгон системы даже в условиях многозадачности отвечают 4 ядра процессора, и оперативная память типа DDR3 на 2 ГБ. Причем последнюю можно увеличить посредством карт памяти до 8 ГБ. Если вы еще захотите усилить быстродействие ноутбука, то воспользуйтесь возможностью подключения к нему твердотельных накопителей (SSD), чем объем не должен превышать 32 ГБ. Благодаря 2 портам USB 2.0 можете подключать к Irbis NB31 флеш-карты. С встроенными веб-камерой и микрофоном вы можете использовать устройство для проведения видеоконференций и общения в чатах. 3 ч 30 мин — таково примерное время автономной работы ноутбука благодаря емкости аккумулятора в 10000 мАч.', 11499),
(3, 'Новый товар', 'Краткое', NULL, 'таблица', 'подробное', 1000);

/*!40000 ALTER TABLE goods ENABLE KEYS */;

-- 
-- Вывод данных для таблицы order_status
--

/*!40000 ALTER TABLE order_status DISABLE KEYS */;
INSERT INTO order_status VALUES
(1, 'Новый', 'ваш заказ обрабатывается оператором. Ожидайте звонка или смс на телефон.'),
(2, 'В пути', 'ваш заказ находится в пути на магазин, с которого будет осуществляться выдача или доставка.'),
(3, 'Ждет оплаты', 'ваш заказ находится в магазине, с которого будет осуществляться выдача или доставка. Необходимо оплатить заказ при получении.'),
(4, 'Готов к выдаче', 'ваш заказ успешно оплачен и находится в магазине, с которого будет осуществляться выдача или доставка.'),
(5, 'Завершен', 'ваш заказ оплачен и выдан.'),
(6, 'Отменен', 'ваш заказ отменен, резервы на товар больше не действуют.');

/*!40000 ALTER TABLE order_status ENABLE KEYS */;

-- 
-- Вывод данных для таблицы pictures
--

/*!40000 ALTER TABLE pictures DISABLE KEYS */;
INSERT INTO pictures VALUES
(1, 'foto\\', '5fb81579.jpg', 198307, 0, NULL, 1, 0),
(2, 'foto\\', '5fb8157e.jpg', 158333, 0, NULL, 1, 0),
(3, 'foto\\', '5fb81581.jpg', 146557, 0, NULL, 1, 0),
(4, 'foto\\', '5fb815c4.jpg', 185095, 0, NULL, 2, 0),
(5, 'foto\\', '5fb815c8.jpg', 190431, 0, NULL, 2, 0),
(6, 'foto\\', '5fb815cc.jpg', 257377, 0, NULL, 2, 0),
(7, 'foto\\', '5fb9f189.jpg', 19376, 0, NULL, 3, 0),
(8, 'foto\\', '5fbabf25.jpg', 422017, 0, NULL, 2, 0);

/*!40000 ALTER TABLE pictures ENABLE KEYS */;

-- 
-- Вывод данных для таблицы users
--

/*!40000 ALTER TABLE users DISABLE KEYS */;
INSERT INTO users VALUES
(1, 'admin', 'a$pG0&32*G!Hs0192023a7bbd73250516f069df18b500a$pG0&32*G!Hs', 'Admin Account', 1),
(2, 'user', 'a$pG0&32*G!Hs6ad14ba9986e3615423dfca256d04e3fa$pG0&32*G!Hs', 'Иванов Иван', 0),
(3, 'dserov', 'a$pG0&32*G!Hs81e2953b18e90317a8cd696ecaff02dba$pG0&32*G!Hs', 'DSerov', 0),
(4, 'qwe', 'a$pG0&32*G!Hs76d80224611fc919a5d54f0ff9fba446a$pG0&32*G!Hs', 'qwe', 0),
(5, 'qwe1', 'a$pG0&32*G!Hsa51c008b213c7a0462e49895df164b50a$pG0&32*G!Hs', 'qwe1', 0),
(6, 'asd', 'a$pG0&32*G!Hs7815696ecbf1c96e6894b779456d330ea$pG0&32*G!Hs', 'asd', 0),
(7, 'zxc', 'a$pG0&32*G!Hs5fa72358f0b4fb4f2c5d7de8c9a41846a$pG0&32*G!Hs', 'zxc', 0);

/*!40000 ALTER TABLE users ENABLE KEYS */;

-- 
-- Вывод данных для таблицы cart
--

-- Таблица internet_shop.cart не содержит данных

-- 
-- Вывод данных для таблицы orders
--

/*!40000 ALTER TABLE orders DISABLE KEYS */;
INSERT INTO orders VALUES
(1, 19950, '2020-11-25 18:28:49', 2, 1),
(2, 17574.05, '2020-11-25 18:35:51', 2, 2),
(3, 50346.2, '2020-11-25 18:37:41', 2, 1),
(6, 7600, '2020-11-25 22:07:04', 2, 2),
(7, 3800, '2020-11-26 13:38:59', 4, 3);

/*!40000 ALTER TABLE orders ENABLE KEYS */;

-- 
-- Вывод данных для таблицы orders_detail
--

/*!40000 ALTER TABLE orders_detail DISABLE KEYS */;
INSERT INTO orders_detail VALUES
(1, 1, 1, 1, 7000, 350, 6650, 6650, NULL),
(2, 1, 1, 2, 7000, 350, 6650, 13300, NULL),
(3, 2, 1, 1, 7000, 350, 6650, 6650, NULL),
(4, 2, 2, 1, 11499, 574.95, 10924.05, 10924.05, NULL),
(5, 3, 1, 1, 7000, 350, 6650, 6650, NULL),
(6, 3, 2, 4, 11499, 574.95, 10924.05, 43696.2, NULL),
(7, 6, 1, 1, 7000, 350, 6650, 6650, 'Перманентная скидка на всё 5%!'),
(8, 6, 3, 1, 1000, 50, 950, 950, 'Перманентная скидка на всё 5%!'),
(9, 7, 3, 4, 1000, 50, 950, 3800, 'Перманентная скидка на всё 5%!');

/*!40000 ALTER TABLE orders_detail ENABLE KEYS */;

-- 
-- Восстановить предыдущий режим SQL (SQL mode)
-- 
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;