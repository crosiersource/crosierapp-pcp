START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_program WHERE uuid = '235a672b-1385-4886-a413-8127a38f4c7b';
DELETE FROM cfg_entmenu WHERE uuid = '39673189-1ae4-4823-affe-cd806a9510ce';





INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('235a672b-1385-4886-a413-8127a38f4c7b','COISAS [LIST]', '/coisa/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('39673189-1ae4-4823-affe-cd806a9510ce', 'Coisas', 'fas fa-drumstick-bite', 'ENT', '235a672b-1385-4886-a413-8127a38f4c7b', '92f4e43c-cdd9-45fd-9077-cba05cbcfbf3', 1 , null, now(), now(), 1, 1, 1);



COMMIT;
