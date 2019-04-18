START TRANSACTION;

-- app                   bfacf76d-2ff2-42c2-854c-bb5b10d78c92
-- program               5875756c-d16d-4906-b4d0-fd60db47ba21
-- entMenu (CrosierCore) 0cfdd630-4382-4a6a-beed-a0e5ec73f8df
-- entMenu (Raíz do App) 90a155bf-0d77-499c-8465-330bc4ce1dd9
-- entMenu (Dashboard)   f9e6121f-d696-49dc-90c4-110b5d15b891

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_app WHERE uuid = 'bfacf76d-2ff2-42c2-854c-bb5b10d78c92';
DELETE FROM cfg_program WHERE uuid = '5875756c-d16d-4906-b4d0-fd60db47ba21';
DELETE FROM cfg_entmenu WHERE uuid = '0cfdd630-4382-4a6a-beed-a0e5ec73f8df';
DELETE FROM cfg_entmenu WHERE uuid = '90a155bf-0d77-499c-8465-330bc4ce1dd9';
DELETE FROM cfg_entmenu WHERE uuid = 'f9e6121f-d696-49dc-90c4-110b5d15b891';


INSERT INTO cfg_app(uuid,nome,obs,default_entmenu_uuid,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id) 
VALUES ('bfacf76d-2ff2-42c2-854c-bb5b10d78c92','PCP','CrosierApp para Controle de Produção','92f4e43c-cdd9-45fd-9077-cba05cbcfbf3',now(),now(),1,1,1);

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('5875756c-d16d-4906-b4d0-fd60db47ba21','Dashboard - PCP', '/', 'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', null, now(), now(), 1, 1, 1);



-- Entrada de menu para o MainMenu do Crosier com apontamento para o Dashboard deste CrosierApp (É EXIBIDO NO MENU DO CROSIER-CORE)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('0cfdd630-4382-4a6a-beed-a0e5ec73f8df','PCP','fas fa-columns','CROSIERCORE_APPENT','5875756c-d16d-4906-b4d0-fd60db47ba21',null,0,null,now(),now(),1,1,1);

-- Entrada de menu raíz para este CrosierApp (NÃO É EXIBIDO)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('90a155bf-0d77-499c-8465-330bc4ce1dd9','PCP - MainMenu','','PAI','',null,0,null,now(),now(),1,1,1);

-- Entrada de menu para o menu raíz deste CrosierApp com apontamento para o Dashboard deste CrosierApp TAMBÉM! (É EXIBIDO COMO PRIMEIRO ITEM DO MENU DESTE CROSIERAPP)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('f9e6121f-d696-49dc-90c4-110b5d15b891','Dashboard','fas fa-columns','ENT','5875756c-d16d-4906-b4d0-fd60db47ba21','90a155bf-0d77-499c-8465-330bc4ce1dd9',0,null,now(),now(),1,1,1);



COMMIT;
