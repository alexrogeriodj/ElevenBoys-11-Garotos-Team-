alter table post add views numeric(10);

create table architect (
	architect_id numeric(4),
	name varchar(80),
	email varchar(80),
	description varchar(1000),
	phone varchar(15),
	cellphone varchar(15),
	image_id numeric(8),
	status numeric(1),
	date_added datetime,
	date_modified datetime,
	primary key(architect_id),
	foreign key(image_id) references image(image_id)
);

alter table post add architect_id numeric(4);
alter table post add foreign key (architect_id) references architect (architect_id);

insert into config (config_id, name, description, config_type_id, content, account_id, date_added, date_modified) values
	(28, 'twitter-app-api-key', 'Twitter App - API key', 1, '', 1, now(), now()),
	(29, 'twitter-app-api-secret', 'Twitter App - API secret', 1, '', 1, now(), now()),
	(30, 'twitter-app-access-token', 'Twitter App - Access token', 1, '', 1, now(), now()),
	(31, 'twitter-app-access-token-secret', 'Twitter App - Access token secret', 1, '', 1, now(), now());
	
create table seo (
	seo_id numeric(10),
	query varchar(250),
	title varchar(70),
	description varchar(170),
	keywords varchar(200),
	robots varchar(30),
	primary key(seo_id)
);

update config set name = 'newsletter-subscriber-email-to', description = 'Newsletter - Nova assinatura: Email para' where config_id = 19;
update config set name = 'newsletter-subscriber-subject', description = 'Newsletter - Nova assinatura: Título email'  where config_id = 20;

insert into config (config_id, name, description, config_type_id, content, account_id, date_added, date_modified) values
	(32, 'newsletter-confirm-subject', 'Newsletter - Confirmação: Título do email', 1, 'Solicitamos confirmar a assinatura', 1, now(), now()),
	(33, 'newsletter-confirm-success', 'Newsletter - Confirmação: Mensagem de sucesso', 1, 'Obrigado por assinar a newsletter do Adoro Decorar!', 1, now(), now());

alter table newsletter drop column name;
alter table newsletter add column first_name varchar(60);
alter table newsletter add column last_name varchar(60);
alter table newsletter add column gender char(1);
alter table newsletter add column birthday date;
alter table newsletter add column cep varchar(9);
alter table newsletter add column code varchar(32);

insert into static_block (static_block_id, name, title, content, status, date_added, date_modified) values 
	(6, 'newsletter-confirmation-text', 'Newsletter - Confirmação', '', 1, now(), now());

drop table slider;

create table slider (
	slider_id numeric(2),
	identifier varchar(30),
	name varchar(100),
	width numeric(4),
	height numeric(4),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(slider_id),
	foreign key(account_id) references account(account_id)
);

create table slider_item (
	slider_item_id numeric(4),
	slider_id numeric(2),
	title varchar(100),
	link varchar(150),
	new_tab numeric(1),
	text varchar(80),
	image_id numeric(8),
	sort_order numeric(2),
	status numeric(1),
	account_id numeric(8),
	date_added datetime,
	date_modified datetime,
	primary key(slider_item_id),
	foreign key(slider_id) references slider(slider_id),
	foreign key(image_id) references image(image_id),
	foreign key(account_id) references account(account_id)
);

insert into banner_type (banner_type_id, name, width, height) values 
	(2, 'Banner Produto (300x400)', 300, 400);