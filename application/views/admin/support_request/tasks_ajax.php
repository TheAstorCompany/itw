				<?php if (isset($tasks) && count($tasks) > 0) {?>
				<strong>Tasks</strong>
                	<ol>
                        <?php foreach($tasks as $index=>$task) { ?>
                		<li task_k="<?php echo $task->id; ?>">
                            <?php foreach($task as $prop=>$val) {
                                echo '<input type="hidden" name="existing_tasks['.$index.']['.$prop.']" value="'.$val.'" />'."\n";
                            }
                            ?>
                            <strong><?php echo $data->SupportRequestServiceTypes[$task->purposeId];?></strong> for
                            <strong><?php echo $task->purposeType?'Recycling':'Waste';?></strong> &nbsp;&nbsp;&#8226;&nbsp;&nbsp; <strong><?php echo $task->quantity; ?></strong>
                            <strong><?php
                                if (property_exists($task, 'containerId') && ($task->containerId !== NULL)) {
                                    echo ' - ' . $data->SupportRequestContainersAll[$task->containerId];
                                }
                            ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;<a href="Javascript:void(0)" onclick="deleteTask('<?php echo $task->id; ?>')">Delete</a><br>
                            <?php if ($task->serviceDate) {?>Service<strong> <?php echo $task->serviceDate; ?>, </strong><?php } ?>
                            <?php if ($task->deliveryDate) {?>Delivery<strong> <?php echo $task->deliveryDate; ?>, </strong><?php } ?>
                            <?php if ($task->removalDate) {?>Removal<strong> <?php echo $task->removalDate; ?>, </strong><?php } ?>
                            <?php echo $task->description;?>
                        </li>
					    <?php } ?>
              	    </ol>
              	<?php } ?>
