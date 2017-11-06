<?php
/**
 * VI Revenue
 */
?>
  
    <div id="quads-vi-revenue-wrapper" >
        <div style="clear:both;">
            <strong>vi Stories</strong> is a native video unit, creating a premium ad unit opportunity and monetizing it. 
            It engages your users and increase the time on site due to <strong>contextual video content.</strong>
        </div>
           
        <div id="quads-vi-revenue-sum-wrapper" style="float:left;width:50%;">
            Total earnings<br>
            <span id="quads-vi-revenue-sum">
            <?php 
            global $quads;
            $revenue = json_decode($quads->vi->getRevenue()); 
            $body = json_decode($revenue->body);
            echo isset($body->data->netRevenue) ? '$' . $body->data->netRevenue : 'Can not retrive data';
            
            //var_dump($revenue);
            //echo $revenue['body'];
            ?>
            </span>
        </div>
        <div style="position: relative; height:200px; width:300px">
            <canvas id="quads-vi-revenue" width="300" height="200"></canvas>
        </div>
    </div>
<div style="clear:both;"></div>