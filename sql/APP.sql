START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

DELETE
FROM cfg_app
WHERE uuid = 'bfacf76d-2ff2-42c2-854c-bb5b10d78c92';


INSERT INTO cfg_app(uuid, nome, obs, inserted, updated, estabelecimento_id, user_inserted_id,
                    user_updated_id)
VALUES ('bfacf76d-2ff2-42c2-854c-bb5b10d78c92', 'crosierapp-pcp', 'CrosierApp para Controle de Produção',
        now(), now(), 1, 1, 1);



COMMIT;
