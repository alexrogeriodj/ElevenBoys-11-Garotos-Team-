create table state (
	state_id char(2),
	name varchar(25),
	primary key(state_id)
);

create table account_type (
	account_type_id numeric(2),
	name varchar(50),
	primary key(account_type_id)
);

create table account (
	account_id numeric(8),
	username varchar(60),
	password varchar(32),
	account_type_id numeric(2),
	name varchar(60),
	email varchar(60),
	phone varchar(15),
	birthday date,
	address varchar(200),
	city varchar(50),
	state_id char(2),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(account_id),
	foreign key(account_type_id) references account_type(account_type_id),
	foreign key(state_id) references state(state_id)
);

create table config_type (
	config_type_id numeric(2),
	name varchar(50),
	primary key(config_type_id)
);

create table config (
	config_id numeric(2),
	name varchar(32),
	description varchar(150),
	help_info varchar(500),
	config_type_id numeric(2),
	content varchar(250),
	sort_order numeric(2),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(config_id),
	foreign key(account_id) references account(account_id),
	foreign key(config_type_id) references config_type(config_type_id)
);

create table config_mail (
	config_mail_id numeric(2),
	name varchar(32),
	mailer varchar(8),
	authenticate char(1),
	charset varchar(12),
	port numeric(4),
	security char(3),
	host varchar(80),
	username varchar(80),
	password varchar(20),
	from_email varchar(80),
	from_name varchar(80),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(config_mail_id),
	foreign key(account_id) references account(account_id)
);

create table image (
	image_id numeric(8),
	name varchar(100),
	legend varchar(250),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	primary key(image_id),
	foreign key(account_id) references account(account_id)
);

create table page (
	page_id numeric(6),
	title varchar(70),
	content text,
	type char(1),
	fixed char(1),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(page_id),
	foreign key(account_id) references account(account_id)
);

create table static_block (
	static_block_id numeric(4),
	name varchar(32),
	title varchar(100),
	content text,
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(static_block_id)
);

create table url_alias (
	url_alias_id numeric(10),
	query varchar(250),
	alias varchar(250),
	primary key(url_alias_id)
);

create table banner_format (
	banner_format_id numeric(2),
	name varchar(30),
	primary key(banner_format_id)
);

create table banner_type (
	banner_type_id numeric(2),
	name varchar(30),
	width numeric(4),
	height numeric(4),
	primary key(banner_type_id)
);

create table banner (
	banner_id numeric(8),
	banner_format_id numeric(2),
	banner_type_id numeric(2),
	title varchar(80),
	link varchar(150),
	new_tab char(1),
	image_id numeric(8),
	content text,
	width numeric(4),
	height numeric(4),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(banner_id),
	foreign key(banner_format_id) references banner_format(banner_format_id),
	foreign key(banner_type_id) references banner_type(banner_type_id),
	foreign key(image_id) references image(image_id)
);

create table banner_stats (
	banner_stats_id numeric(10),
	banner_id numeric(8),
	datetime datetime,
	ip varchar(40),
	primary key(banner_stats_id),
	foreign key(banner_id) references banner(banner_id)
);

create table category (
	category_id numeric(4),
	name varchar(200),
	description varchar(1000),
	status numeric(1),
	sort_order numeric(2),
	date_added datetime,
	date_modified datetime,
	primary key(category_id)
);

create table video (
	video_id numeric(8),
	title varchar(200),
	type char(1),
	link varchar(200),
	image_id numeric(8),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(video_id),
	foreign key(image_id) references image(image_id)
);

create table post (
	post_id numeric(10),
	title varchar(200),
	content text,
	summary varchar(300),
	tags varchar(200),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(post_id),
	foreign key(account_id) references account(account_id)
);

create table post_category (
	post_id numeric(10),
	category_id numeric(4),
	primary key(post_id, category_id),
	foreign key(post_id) references post(post_id),
	foreign key(category_id) references category(category_id)
);

create table post_video (
	post_id numeric(10),
	video_id numeric(8),
	primary key(post_id, video_id),
	foreign key(post_id) references post(post_id),
	foreign key(video_id) references video(video_id)
);

create table post_image (
	post_id numeric(10),
	image_id numeric(8),
	featured numeric(1),
	primary key(post_id, image_id),
	foreign key(post_id) references post(post_id),
	foreign key(image_id) references image(image_id)
);

create table newsletter (
	newsletter_id numeric(8),
	name varchar(60),
	email varchar(60),
	ip varchar(40),
	date_added datetime,
	primary key(newsletter_id)
);

create table slider (
	slider_id numeric(6),
	title varchar(100),
	link varchar(150),
	text varchar(80),
	image_id numeric(8),
	sort_order numeric(2),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(slider_id),
	foreign key(image_id) references image(image_id),
	foreign key(account_id) references account(account_id)
);





create table schedule (
	id numeric(8),
	title varchar(60),
	content text,
	account_id numeric(2),
	date_ini datetime,
	date_fim datetime,	
	room_id char(2),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(id)
);


create table room (
	id numeric(8),
	title varchar(60),
	content text,
	account_id numeric(2),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(id)
);