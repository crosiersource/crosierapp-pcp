START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;

INSERT INTO cfg_app(id,uuid,nome,obs,default_entmenu_uuid,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id) 
VALUES(null,_____UUID DO APP_____,_____NOME DO APP_____,_____OBS_____,_____UUID DA ENTRADA DE MENU PADRÃO_____,now(),now(),1,1,1);

INSERT INTO cfg_program(id, uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES (null, _____UUID DO PROGRAMA DA DASHBOARD DESTE APP_____,_____DESCRIÇÃO DO PROGRAMA DA DASHBOARD_____, '/', _____UUID DO APP_____, null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(id,uuid,label,icon,tipo,program_uuid,pai_id,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES (null,_____UUID DA ENTRADA DE MENU PARA A DASHBOARD_____,_____LABEL PARA EXIBIR NO MENU_____,'fas fa-columns','ENT',_____UUID DO PROGRAMA DA DASHBOARD_____,null,0,null,now(),now(),1,1,1);


COMMIT;
