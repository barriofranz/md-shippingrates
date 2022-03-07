<h2 class="title">
    Master Shipping Rates
    <div class="row" style="padding-top: 15px;">
		<button href="#" class="button-primary msr-save-all">Save all</button>
    </div>
</h2>


<?php
foreach ($zones as $zone) {
	if(isset($zone['shipping_methods'])) {
		foreach ($zone['shipping_methods'] as $method) {
            if( $method->id == 'free_shipping') {
                continue;
            }
            // $optSavedName = $method->get_options_save_name();
            $optSavedName = $method->id . '_options-' . $method->instance_id;
            $shippingOptions = get_option( $optSavedName);
            // echo '<pre>';print_r($shippingOptions);echo '</pre>';
            // echo '<pre>';print_r($optSavedName);echo '</pre>';
            // echo '<pre>';print_r($method);echo '</pre>';
            ?>

            <div class="wrap" id="msr-main-div" >
                <div class="md-panel">

                    <div class="msr-body">
                        <div class="md-row">
                            <div class="md-methods-div" data-option="<?= $optSavedName ?>" >
                                <h2><?= $method->title ?></h2>
                                <div class="md-row">
                                    <?php
                                    foreach ($shippingOptions['settings'] as $options){
                                        // echo '<pre>';print_r($options);echo '</pre>';
                                        ?>
                                        <div class="md-options-div single-row" data-row_id="<?= $options['option_id'] ?>">
                                            <span class="option-title"><?= $options['title'] ?></span>
                                            <?php
                                            foreach ($options as $key => $rows){

                                                $inpHiddenName = '';
                                                switch($key) {
                                                    case 'title':$inpHiddenName="option_" . $key;break;
                                                    case 'default':$inpHiddenName=$key . "_select";break;
                                                    case 'recursive':$inpHiddenName=$key . "_op";break;
                                                    default:$inpHiddenName=$key;break;
                                                }

                                                if($key != 'rows') {
                                                    echo '<input type="hidden" name="'.$inpHiddenName.'['.$options['option_id'].']" value="'.$options[$key].'">';
                                                }
                                            }


                                            ?>
                                            <table class="md-table1 single-row msr-table" data-row_id="<?= $options['option_id'] ?>">
                                                <thead>
                                                    <th scope="col" class="manage-column column-cb check-column" style="">
                                                        <label class="screen-reader-text" >Select All</label>
                                                        <input type="checkbox" class="check-all-chk">
                                                    </th>
                                                    <th>Conditions</th>
                                                    <th>Costs</th>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($options['rows'] as $rowKey => $rows){
                                                    $itemDesc = $rows['description'];

                                                    ?>
                                                    <tr>
                                                        <th scope="row" class="check-column">
                                                            <?php
                                                            echo $betrsTableOptions->column_cb(['row_ID'=>$rowKey]);
                                                            ?>
                                                        </th>
                                                        <td>
                                                        <?php
                                                        echo '<input type="hidden" name="option_description['.$options['option_id'].'][]" value="'.$itemDesc.'">';
                                                        $condCount = count( is_array($rows['conditions']) ? $rows['conditions'] : [] );
                                                        for ($ctr=0; $ctr < $condCount; $ctr++){
                                                            $itemConds = $rows['conditions'][$ctr];
                                                            $itemConds['option_ID'] = $options['option_id'];
                                                            $itemConds['row_ID '] = $rowKey;
                                                            echo $betrsTableOptions->generate_conditions_section($itemConds, $options['option_id'], $rowKey, $cond_key = 0 );

                                                        }
                                                        echo $betrsTableOptions->column_conditions([]);
                                                        ?>
                                                        </td>

                                                        <td>
                                                        <?php
                                                        $costCount = count( is_array($rows['costs']) ? $rows['costs'] : [] );
                                                        for ($ctr=0; $ctr < $costCount; $ctr++){
                                                            $itemCosts = $rows['costs'][$ctr];
                                                            $itemCosts['option_ID'] = $options['option_id'];
                                                            $itemCosts['row_ID'] = $rowKey;
                                                            echo $betrsTableOptions->generate_cost_section($itemCosts, $options['option_id'], $rowKey, $cond_key = 0 );
                                                        }

                                                        echo '<a href="#" class="add_table_cost_op">' . __( 'Add another cost', 'be-table-ship' ) . '</a>';
                                                        ?>
                                                        </td>

                                                    </tr>
                                                    <?php

                                                }
                                                ?>
                                                </tbody>

                                                <tfoot>
                                                    <th scope="col" class="manage-column column-cb check-column" style="">
                                                        <label class="screen-reader-text" >Select All</label>
                                                        <input type="checkbox" class="check-all-chk">
                                                    </th>
                                                    <th>Conditions</th>
                                                    <th>Costs</th>
                                                </tfoot>
                                            </table>
                                            <div class="md-row">
                                            <?php
                                                $betrsTableOptions->extra_tablenav( 'bottom' );
                                            ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <button href="#" class="button-primary msr-save-btn" data-option="<?= $optSavedName ?>" >Save</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <?php
		}
	}
}

?>

<div class="row" style="padding-top: 15px;">
    <button href="#" class="button-primary msr-save-all">Save all</button>
</div>
