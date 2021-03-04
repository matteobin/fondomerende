    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<style>
    table thead tr th {
        padding: 9px;
    }
    table tbody tr td {
        padding: 6px;
    }
</style>
<table id="jslicense-labels1" class="one-column-row" border="1">
    <thead>
        <tr>
            <th><?php echo getStringInLang('js-licenses', 4); ?></th>
            <th><?php echo getStringInLang('js-licenses', 5); ?></th>
            <th><?php echo getStringInLang('js-licenses', 6); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $jsScripts = array(
                'add-snack',
                'buy',
                'deposit-or-withdraw',
                'eat',
                'edit-snack',
                'edit-user',
                'format-number-string'
            );
            foreach ($jsScripts as $jsScript):
        ?>
            <tr>
                <td><a href="<?php echo WEB_BASE_DIR; ?>js/<?php echo $jsScript; ?>.min.js"><?php echo $jsScript; ?>.min.js</a></td>
                <td><a href="https://www.gnu.org/licenses/gpl-3.0.html">GPL-v3-or-Later</a></td>
                <td><a href="<?php echo WEB_BASE_DIR; ?>js/<?php echo $jsScript; ?>.js"><?php echo $jsScript; ?>.js</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
