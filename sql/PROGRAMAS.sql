START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_program WHERE uuid = '77d2856a-61b3-4d93-8329-3e6e0923f305';
DELETE FROM cfg_entmenu WHERE uuid = '371a1461-8e66-4633-8ee2-25c5db01a6e6';





INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('77d2856a-61b3-4d93-8329-3e6e0923f305','TIPO INSUMO [LIST]', '/tipoInsumo/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('371a1461-8e66-4633-8ee2-25c5db01a6e6', 'Tipos de Insumos', 'fas fa-cube', 'ENT', '77d2856a-61b3-4d93-8329-3e6e0923f305', '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1 , null, now(), now(), 1, 1, 1);



COMMIT;
