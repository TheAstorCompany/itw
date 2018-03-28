<style type="text/css">
    #audit_log {
        border: 1px solid #cccccc;
    }

    #audit_log th{
        border: 1px solid #cccccc;
        font-weight: bold;
    }

    #audit_log td{
        border: 1px solid #cccccc;
        padding-left: 5px;
        padding-right: 5px;
    }
</style>
<div style="padding-bottom: 5px;">
    <h5>Audit Log</h5>
    <table id="audit_log">
        <tr>
            <th style="width: 120px;">User</th>
            <th style="width: 120px;">Action</th>
            <th style="width: 150px;">Date</th>
        </tr>
        <?php
            foreach($widget_data['rows'] as $row) {
                echo '<tr><td>'.$row->username.'</td><td>'.$row->action.'</td><td>'.$row->dt_us.'</td></tr>'."\n";
            }
        ?>
    </table>
</div>