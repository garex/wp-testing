SET GLOBAL old_passwords=1;
SET GLOBAL secure_auth=1;
FLUSH PRIVILEGES;
select OLD_PASSWORD('oldpw'), PASSWORD('oldpw');

UPDATE `mysql`.`user` SET `Password`=OLD_PASSWORD('some_pass') WHERE `Host`='localhost' and`User`='monty';
-- UPDATE `mysql`.`user` SET `Password`=PASSWORD('oldpw') WHERE `Host`='%' and`User`='oldpw';
UPDATE `mysql`.`user` SET `plugin`='' WHERE `Host`='%' and`User`='oldpw';

select * from `mysql`.`user`;