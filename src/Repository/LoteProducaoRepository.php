<?php

namespace App\Repository;


use App\Entity\LoteProducao;
use App\Entity\LoteProducaoItem;
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
    public function getTiposInsumosPorLote(LoteProducao $loteProducao): array
    {
        $sql = 'select ti.id, ti.descricao
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
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('descricao', 'descricao');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $loteProducao->getId());
        return $query->getResult();
    }


    /**
     * @param LoteProducao $loteProducao
     * @return array
     */
    public function getTamanhosPorLote(LoteProducao $loteProducao): array
    {
        $sql = 'select 
    gt.id,
	gt.tamanho	
from 
    prod_fichatecnica_item_qtde fiq, 
    prod_fichatecnica_item fi, 
    prod_fichatecnica f, 
    prod_lote_producao l, 
    prod_lote_producao_item li,
    prod_lote_producao_item_qtde liq,
    prod_insumo i,
    prod_insumo_preco ip,
    prod_tipo_insumo ti,
    est_grade_tamanho gt    
    
where 
    l.id = li.lote_producao_id and
    liq.lote_producao_item_id = li.id and
    li.fichatecnica_id = f.id and
    fi.fichatecnica_id = f.id and
    fiq.fichatecnica_item_id = fi.id and
    fi.insumo_id = i.id and
    liq.grade_tamanho_id = fiq.grade_tamanho_id and
    ip.insumo_id = i.id and
    i.tipo_insumo_id = ti.id and
    gt.id = liq.grade_tamanho_id and
    gt.id = fiq.grade_tamanho_id and   
    l.id = ? and
    ip.atual is true
GROUP BY gt.id
ORDER BY gt.ordem';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('tamanho', 'tamanho');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $loteProducao->getId());
        return $query->getResult();
    }


    /**
     * @param LoteProducaoItem $loteProducaoItem
     * @param int $tipoInsumoId
     * @return array
     */
    public function getInsumosPorLoteItemETipoInsumo(LoteProducaoItem $loteProducaoItem, int $tipoInsumoId): array
    {
        $sql = '
select 
	i.id,
	i.descricao,
	u.casas_decimais
from 
    prod_fichatecnica_item fi, 
    prod_fichatecnica f, 
    prod_lote_producao l, 
    prod_lote_producao_item li,
    prod_insumo i,
    est_unidade_produto u,
    prod_tipo_insumo ti
where 
    l.id = li.lote_producao_id and
    li.fichatecnica_id = f.id and
    fi.fichatecnica_id = f.id and
    fi.insumo_id = i.id and
    i.tipo_insumo_id = ti.id and
    i.unidade_produto_id = u.id and    
    li.id = :loteItemId and
    ti.id = :tipoInsumoId
GROUP BY i.id
ORDER BY i.descricao;        
        ';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('descricao', 'descricao');
        $rsm->addScalarResult('casas_decimais', 'casas_decimais');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('loteItemId', $loteProducaoItem->getId());
        $query->setParameter('tipoInsumoId', $tipoInsumoId);
        return $query->getResult();
    }


    /**
     * @param LoteProducao $loteProducao
     * @return array
     */
    public function buildTotalizPorTipoInsumo(LoteProducao $loteProducao): array
    {
        $dados = [];

        $tiposInsumos = $this->getTiposInsumosPorLote($loteProducao);

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

            $r = $query->getResult();

            $dados[$tipoInsumoDescricao]['insumos'] = $r;
            $total = 0.0;
            foreach ($r as $insumo) {
                $total += $insumo['total'];
            }
            $dados[$tipoInsumoDescricao]['total'] = $total;


        }
        return $dados;
    }


    /**
     * @param LoteProducaoItem $loteProducaoItem
     * @param int $insumoId
     * @param int $gradeTamanhoId
     * @return float
     */
    public function getTotalPorLoteItemEInsumoETamanho(LoteProducaoItem $loteProducaoItem, int $insumoId, int $gradeTamanhoId): float
    {
        $dados = [];

        $sql =
            'select 	
	sum(liq.qtde * fiq.qtde) as total
from 
    prod_fichatecnica_item_qtde fiq, 
    prod_fichatecnica_item fi, 
    prod_fichatecnica f, 
    prod_lote_producao l, 
    prod_lote_producao_item li,
    prod_lote_producao_item_qtde liq,
    prod_insumo i,
    prod_insumo_preco ip,
    prod_tipo_insumo ti,
    est_grade_tamanho gt
where 
    l.id = li.lote_producao_id and
    liq.lote_producao_item_id = li.id and
    li.fichatecnica_id = f.id and
    fi.fichatecnica_id = f.id and
    fiq.fichatecnica_item_id = fi.id and
    fi.insumo_id = i.id and
    liq.grade_tamanho_id = fiq.grade_tamanho_id and
    ip.insumo_id = i.id and
    i.tipo_insumo_id = ti.id and
    gt.id = liq.grade_tamanho_id and
    gt.id = fiq.grade_tamanho_id and
    li.id = :loteItemId and
    i.id = :insumoId and
    gt.id = :gradeTamanhoId and
    ip.atual is true';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('loteItemId', $loteProducaoItem->getId());
        $query->setParameter('insumoId', $insumoId);
        $query->setParameter('gradeTamanhoId', $gradeTamanhoId);
        return $query->getResult()[0]['total'] ?? 0.0;
    }


    /**
     * @param LoteProducao $loteProducao
     * @return array
     */
    public function buildTotalizPorItemETipoInsumoEGrade(LoteProducao $loteProducao): array
    {
        $dados = [];

        $tiposInsumos = $this->getTiposInsumosPorLote($loteProducao);
        $tamanhos = $this->getTamanhosPorLote($loteProducao);

        $itens = $loteProducao->getItens();

        /** @var LoteProducaoItem $item */
        foreach ($itens as $item) {

            $rPorTipoInsumo = [];

            foreach ($tiposInsumos as $tipoInsumo) {

                $insumos = $this->getInsumosPorLoteItemETipoInsumo($item, $tipoInsumo['id']);

                $rInsumo = [];

                foreach ($insumos as $insumo) {
                    $totaisPorTamanho = [];
                    $rInsumo[$insumo['id']]['insumo'] = $insumo;
                    foreach ($tamanhos as $tamanho) {
                        $qtde = $this->getTotalPorLoteItemEInsumoETamanho($item, $insumo['id'], $tamanho['id']);
                        $totaisPorTamanho[$tamanho['tamanho']] = $qtde;
                    }
                    $rInsumo[$insumo['id']]['totaisPorTamanho'] = $totaisPorTamanho;
                }

                $rPorTipoInsumo[$tipoInsumo['descricao']] = $rInsumo;

            }

            $dados[$item->getId()] = [
                'item' => ['descricao' => $item->getFichaTecnica()->getDescricao()],
                'tiposInsumos' => $rPorTipoInsumo
            ];

        }

        return $dados;

    }


}
