
create table comments(
	comment_id int unsigned not null auto_increment primary key,
	user_id int unsigned not null,
	content text not null,
	date timestamp default current_timestamp not null
);

create table users(
	user_id int unsigned not null  primary key,
	is_ban bool default 1 not null,
	ip char(45),
	time timestamp default current_timestamp not null
);

create table admins(
	admin_id char(16),
	password char(41)
);

insert into admins values ('admin', sha1('admin'));