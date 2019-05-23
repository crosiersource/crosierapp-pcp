<?php

namespace App\Repository;


use App\Entity\LoteProducao;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Repository para a entidade LoteProducao.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class LoteProducaoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return LoteProducao::class;
    }


    /**
     * @param LoteProducao $loteProducao
     * @return array
     */
    public function buildDadosPorTipoInsumo(LoteProducao $loteProducao): array
    {

        $dados = [];

        $sql = 'select i.tipo_insumo_id, ti.descricao as descricao
        from
            prod_fichatecnica_item fi,
            prod_fichatecnica f,
            prod_lote_producao l,
            prod_lote_producao_item li,
            prod_insumo i,
            prod_tipo_insumo ti
        where
            l.id = li.lote_producao_id and
            li.fichatecnica_id = f.id and
            fi.fichatecnica_id = f.id and
            fi.insumo_id = i.id and
            i.tipo_insumo_id = ti.id and
            l.id = ?
        GROUP BY i.tipo_insumo_id';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('tipo_insumo_id', 'tipo_insumo_id');
        $rsm->addScalarResult('descricao', 'descricao');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $loteProducao->getId());
        $tiposInsumos = $query->getResult();


        $sqlTotalInsumo =
            'select i.id, i.descricao, sum(fiq.qtde * liq.qtde) as qtde_total, ip.preco_custo, ((sum(fiq.qtde * liq.qtde)) * ip.preco_custo) as total
            from 
                prod_fichatecnica_item_qtde fiq, 
                prod_fichatecnica_item fi, 
                prod_fichatecnica f, 
                prod_lote_producao l, 
                prod_lote_producao_item li,
                prod_lote_producao_item_qtde liq,
                prod_insumo i,
                prod_insumo_preco ip
            where 
                l.id = li.lote_producao_id and
                liq.lote_producao_item_id = li.id and
                li.fichatecnica_id = f.id and
                fi.fichatecnica_id = f.id and
                fiq.fichatecnica_item_id = fi.id and
                fi.insumo_id = i.id and
                liq.grade_tamanho_id = fiq.grade_tamanho_id and
                l.id = :loteProducaoId and
                i.tipo_insumo_id = :tipoInsumoId and 
                ip.insumo_id = i.id and
                ip.atual is true
            GROUP BY i.id, ip.id
            ORDER BY i.descricao
            ';

        foreach ($tiposInsumos as $tipoInsumo) {
            $tipoInsumoId = $tipoInsumo['tipo_insumo_id'];
            $tipoInsumoDescricao = $tipoInsumo['descricao'];
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('descricao', 'descricao');
            $rsm->addScalarResult('qtde_total', 'qtde_total');
            $rsm->addScalarResult('preco_custo', 'preco_custo');
            $rsm->addScalarResult('total', 'total');
            $query = $this->getEntityManager()->createNativeQuery($sqlTotalInsumo, $rsm);
            $query->setParameter('loteProducaoId', $loteProducao->getId());
            $query->setParameter('tipoInsumoId', $tipoInsumoId);

            $dados[$tipoInsumoDescricao] = $query->getResult();



        }

        return $dados;


    }


}
