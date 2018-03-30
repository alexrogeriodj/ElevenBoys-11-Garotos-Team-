insert into state (state_id, name) values 
	('AC', 'Acre'), ('AL', 'Alagoas'), ('AP', 'Amapá'), ('AM', 'Amazonas'), ('BA', 'Bahia'), 
	('CE', 'Ceará'), ('DF', 'Distrito Federal'), ('ES', 'Espírito Santo'), ('GO', 'Goiás'), 
	('MA', 'Maranhão'), ('MT', 'Mato Grosso'), ('MS', 'Mato Grosso do Sul'), ('MG', 'Minas Gerais'), 
	('PA', 'Pará'), ('PB', 'Paraíba'), ('PR', 'Paraná'), ('PE', 'Pernambuco'), ('PI', 'Piauí'), 
	('RJ', 'Rio de Janeiro'), ('RN', 'Rio Grande do Norte'), ('RS', 'Rio Grande do Sul'), 
	('RO', 'Rondônia'), ('RR', 'Roraima'), ('SC', 'Santa Catarina'), ('SP', 'São Paulo'), 
	('SE', 'Sergipe'), ('TO', 'Tocantins');
	
insert into account_type (account_type_id, name) values 
	(1, 'Administrador'), 
	(2, 'Usuário');

insert into account (account_id, username, password, account_type_id, name, email, phone, 
	birthday, city, state_id, status, date_added, date_modified) values 
	(1, 'admin', '3cf108a4e0a498347a5a75a792f23212', 1, 'Administrador', 'contato@universaldecor.com.br', 
	'(41) 3233-1001', '2014-01-01', 'Curitiba', 'PR', 1, now(), now());
	
insert into config_type (config_type_id, name) values 
	(1, 'Text'), 
	(2, 'Textarea'), 
	(3, 'Radio'), 
	(4, 'Checkbox'), 
	(5, 'Radio/Text'), 
	(6, 'Radio/Textarea'), 
	(7, 'Checkbox/Text'), 
	(8, 'Checkbox/Textarea');

insert into config (config_id, name, description, config_type_id, content, account_id, date_added, date_modified) values
	(1, 'site-url', 'Site: URL do site', 1, '', 1, now(), now()),
	(2, 'site-name', 'Site: Nome do site', 1, 'Adoro Decorar', 1, now(), now()),
	(3, 'friendly-url', 'SEO: Usar URL amigável (ON/OFF)', 3, 'ON', 1, now(), now()),
	(4, 'meta-description', 'Meta description padrão', 2, '', 1, now(), now()),
	(5, 'google-analytics', 'Google Analytics: Código da conta (Ex: UA-17211024-1)', 5, 'OFF', 1, now(), now()),
	(6, 'google-site-verification', 'Google site verification', 5, 'OFF', 1, now(), now()),
	(7, 'facebook-app-id', 'Facebook: ID do aplicativo', 5, 'OFF', 1, now(), now()),
	(8, 'facebook-app', 'Facebook: URL do perfil', 5, 'OFF', 1, now(), now()),
	(9, 'link-facebook', 'Rede Social: Facebook', 5, 'OFF', 1, now(), now()),
	(10, 'link-twitter', 'Rede Social: Twitter', 5, 'OFF', 1, now(), now()),
	(11, 'link-pinterest', 'Rede Social: Pinterest', 5, 'OFF', 1, now(), now()),
	(12, 'link-instagram', 'Rede Social: Instagram', 5, 'OFF', 1, now(), now()),
	(13, 'link-google-plus', 'Rede Social: Google Plus', 5, 'OFF', 1, now(), now()),
	(14, 'link-youtube', 'Rede Social: Youtube', 5, 'OFF', 1, now(), now()),
	(15, 'link-vimeo', 'Rede Social: Vimeo', 5, 'OFF', 1, now(), now()),
	(16, 'contact-email-to', 'Contato: Email para', 1, '', 1, now(), now()),
	(17, 'contact-subject', 'Contato: Titulo email', 1, 'Contato', 1, now(), now()),
	(18, 'contact-success', 'Contato: Email mensagem', 1, 'Dados de contato enviado com sucesso.', 1, now(), now()),
	(19, 'newsletter-email-to', 'Newsletter: Email para', 1, '', 1, now(), now()),
	(20, 'newsletter-subject', 'Newsletter: Titulo email', 1, 'Newsletter', 1, now(), now()),
	(21, 'newsletter-success', 'Newsletter: Mensagem usuário', 1, 'Seu email foi cadastrado.', 1, now(), now()),
	(22, 'advertise-email-to', 'Anuncie: Email para', 1, '', 1, now(), now()),
	(23, 'advertise-subject', 'Anuncie: Titulo email', 1, 'Anuncie', 1, now(), now()),
	(24, 'advertise-success', 'Anuncie: Mensagem usuário', 1, 'Dados enviados com sucesso.', 1, now(), now()),
	(25, 'tips-category-id', 'Página Dicas: ID da categoria', 1, '', 1, now(), now()),
	(26, 'mailchimp-api-key', 'MailChimp: API Key', 1, '', 1, now(), now()),
	(27, 'mailchimp-list-id', 'MailChimp: ID da lista', 1, '', 1, now(), now());

update config set sort_order = config_id;
	
insert into config_mail (config_mail_id, name, mailer, authenticate, charset, port, security, 
	host, username, password, from_email, from_name, account_id, date_added, date_modified) values 
	(1, 'default', 'smtp', 'S', 'UTF-8', 25, '', '', '', '', '', 'Universal Decor', 1, now(), now());
	
insert into banner_format (banner_format_id, name) values 
	(1, 'Imagem'), (2, 'HTML'); 
	
insert into static_block(static_block_id, name, title, content, status, date_added, date_modified) values 
	(1, 'footer-about', 'Rodapé - Sobre', '', 1, now(), now()),
	(2, 'footer-links', 'Rodapé - Links', '', 1, now(), now()),
	(3, 'footer-pinterest', 'Rodapé - Pinterest', '', 1, now(), now()),
	(4, 'footer-instagram', 'Rodapé - Instagram', '', 1, now(), now());
	
insert into page(page_id, title, content, type, fixed, status, account_id, date_added, date_modified) values
	(1, 'Sobre', '', 'H', 'S', 1, 1, now(), now()),
	(2, 'Política de comentários', '', 'H', 'S', 1, 1, now(), now()),
	(3, 'Licença de uso', '', 'H', 'S', 1, 1, now(), now()),
	(4, 'Contato', '', 'H', 'S', 1, 1, now(), now()),
	(5, 'Anuncie', '', 'H', 'S', 1, 1, now(), now());
	
insert into category(category_id, name, description, status, sort_order, date_added, date_modified) values 
	(1, 'Geral', '', 1, 1, now(), now());
	
insert into url_alias(url_alias_id, query, alias) values
	(1, 'page.php?id=1', 'sobre'),
	(2, 'page.php?id=2', 'politica-de-comentarios'),
	(3, 'page.php?id=3', 'licenca-de-uso'),
	(4, 'page.php?id=4', 'contato'),
	(5, 'page.php?id=5', 'anuncie'),
	(6, 'category.php?id=1', 'categoria/geral');
	
insert into banner_type (banner_type_id, name, width, height) values 
	(1, 'Banner Lateral (300x250)', 300, 250);