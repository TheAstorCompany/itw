<table width="100%" border="0" cellspacing="10" cellpadding="20">
    <tr style="border-bottom: 1px solid #ddd">
        <td>Waste
            <h4>
                <?php echo number_format($CurrentMonthData->waste, 0); ?>
                tons
            </h4>
        </td>
        <td>Recycling
            <h4><?php echo number_format($CurrentMonthData->recycling, 0); ?> tons</h4>
        </td>
        <td>Diversion rate
            <h4><?php echo number_format($CurrentMonthData->diversion, 0); ?>%</h4>
        </td>
    </tr>
    <tr>
        <td>Cost
            <h4>$<?php echo number_format($CurrentMonthData->cost, 0); ?></h4>
        </td>
        <td>Rebate
            <h4>$<?php echo number_format($CurrentMonthData->rebate, 0); ?></h4>
        </td>
        <td>Waste Cost/sq ft&nbsp;|&nbsp;Recycle Cost/sq ft
            <h4>$<?php echo number_format($CurrentMonthData->wasteCostSqFt, 3); ?>&nbsp;|&nbsp;$<?php echo number_format($CurrentMonthData->recyclingCostSqFt, 3); ?></h4>
        </td>
    </tr>
</table>