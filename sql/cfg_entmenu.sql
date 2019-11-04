START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;


--
--
-- Entrada no menu do crosier-core
DELETE
FROM cfg_entmenu
WHERE uuid = 'fd1a7a9f-1a1c-45f2-ae0c-72a07e2ffd6c';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('fd1a7a9f-1a1c-45f2-ae0c-72a07e2ffd6c', 'PCP', 'fas fa-industry', 'ENT', 'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/', '',
        '71d1456b-3a9f-4589-8f71-42bbf6c91a3e', 100, 'background-color: darkslateblue', now(), now(), 1, 1, 1);




-- Entrada de menu raíz para este CrosierApp (NÃO É EXIBIDO)

DELETE
FROM cfg_entmenu
WHERE uuid = '90a155bf-0d77-499c-8465-330bc4ce1dd9';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('90a155bf-0d77-499c-8465-330bc4ce1dd9', 'crosierapp-pcp (Menu Raíz)', '', 'PAI',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '', '', null, 0, null, now(), now(), 1, 1, 1);


-- Tipo de Insumo
DELETE
FROM cfg_entmenu
WHERE uuid = '371a1461-8e66-4633-8ee2-25c5db01a6e6';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('371a1461-8e66-4633-8ee2-25c5db01a6e6', 'Tipos de Insumos', 'fas fa-cube', 'ENT',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/tipoInsumo/list', 'ROLE_PCP_ADMIN',
        '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1, null, now(), now(), 1, 1, 1);


-- Tipo de Artigo
DELETE
FROM cfg_entmenu
WHERE uuid = '774407eb-2b84-4bd0-9af8-f355442538fd';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('774407eb-2b84-4bd0-9af8-f355442538fd', 'Tipos de Artigos', 'fas fa-cubes', 'ENT',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/tipoArtigo/list', 'ROLE_PCP_ADMIN',
        '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1, null, now(), now(), 1, 1, 1);


-- Insumo
DELETE
FROM cfg_entmenu
WHERE uuid = '63594d0b-56ba-416c-9e10-858695bc98d0';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('63594d0b-56ba-416c-9e10-858695bc98d0', 'Insumos', 'fas fa-kaaba', 'ENT',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/insumo/list', 'ROLE_PCP_ADMIN',
        '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1, null, now(), now(), 1, 1, 1);


-- Ficha Técnica
DELETE
FROM cfg_entmenu
WHERE uuid = '6b380d26-b90b-45b5-9a44-e1336df58b32';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('6b380d26-b90b-45b5-9a44-e1336df58b32', 'Fichas Técnicas', 'fas fa-file-powerpoint', 'ENT',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/fichaTecnica/list', 'ROLE_PCP',
        '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1, null, now(), now(), 1, 1, 1);


-- Lotes de Produção
DELETE
FROM cfg_entmenu
WHERE uuid = '417b12af-4b62-47f2-8831-d5a548e641b3';

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, url, roles, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('417b12af-4b62-47f2-8831-d5a548e641b3', 'Lotes de Produção', 'fas fa-industry', 'ENT',
        'bfacf76d-2ff2-42c2-854c-bb5b10d78c92', '/loteProducao/list', 'ROLE_PCP_ADMIN',
        '90a155bf-0d77-499c-8465-330bc4ce1dd9', 1, null, now(), now(), 1, 1, 1);




--
--
--
--
--
--
--
-- cfg_entmenu_locator
--
INSERT INTO cfg_entmenu_locator(menu_uuid, url_regexp, quem, inserted, updated, estabelecimento_id, user_inserted_id,
                                user_updated_id)
VALUES ('90a155bf-0d77-499c-8465-330bc4ce1dd9', '^https://pcp\.', '*', now(), now(), 1, 1, 1);


COMMIT;
