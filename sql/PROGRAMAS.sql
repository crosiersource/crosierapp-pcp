START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;


-- Tipo de Insumo
DELETE FROM cfg_program WHERE uuid = '77d2856a-61b3-4d93-8329-3e6e0923f305';
DELETE FROM cfg_entmenu WHERE uuid = '371a1461-8e66-4633-8ee2-25c5db01a6e6';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('77d2856a-61b3-4d93-8329-3e6e0923f305','TIPO INSUMO [LIST]', '/tipoInsumo/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('371a1461-8e66-4633-8ee2-25c5db01a6e6', 'Tipos de Insumos', 'fas fa-cube', 'ENT', '77d2856a-61b3-4d93-8329-3e6e0923f305', '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1 , null, now(), now(), 1, 1, 1);


-- Tipo de Artigo
DELETE FROM cfg_program WHERE uuid = 'b5e83f8b-ef2e-4eb2-a7c2-1e999c6e1efa';
DELETE FROM cfg_entmenu WHERE uuid = '774407eb-2b84-4bd0-9af8-f355442538fd';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('b5e83f8b-ef2e-4eb2-a7c2-1e999c6e1efa','TIPO ARTIGO [LIST]', '/tipoArtigo/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('774407eb-2b84-4bd0-9af8-f355442538fd', 'Tipos de Artigos', 'fas fa-cubes', 'ENT', 'b5e83f8b-ef2e-4eb2-a7c2-1e999c6e1efa', '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1 , null, now(), now(), 1, 1, 1);


-- Insumo
DELETE FROM cfg_program WHERE uuid = '65f164db-a1a6-4552-a753-dacb9b24258d';
DELETE FROM cfg_entmenu WHERE uuid = '63594d0b-56ba-416c-9e10-858695bc98d0';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('65f164db-a1a6-4552-a753-dacb9b24258d','INSUMO [LIST]', '/insumo/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('63594d0b-56ba-416c-9e10-858695bc98d0', 'Insumos', 'fas fa-kaaba', 'ENT', '65f164db-a1a6-4552-a753-dacb9b24258d', '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1 , null, now(), now(), 1, 1, 1);


-- Ficha Técnica
DELETE FROM cfg_program WHERE uuid = '508a278c-4993-42e9-b7ac-f874f7dc50bf';
DELETE FROM cfg_entmenu WHERE uuid = '6b380d26-b90b-45b5-9a44-e1336df58b32';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('508a278c-4993-42e9-b7ac-f874f7dc50bf','FICHA TECNICA [LIST]', '/fichaTecnica/list', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('6b380d26-b90b-45b5-9a44-e1336df58b32', 'Fichas Técnicas', 'fas fa-file-powerpoint', 'ENT', '508a278c-4993-42e9-b7ac-f874f7dc50bf', '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1 , null, now(), now(), 1, 1, 1);



COMMIT;
