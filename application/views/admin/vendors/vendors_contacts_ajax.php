                <?php if (isset($vendorContacts) && count($vendorContacts) > 0) {?>
                <strong>Current Contacts</strong>
                <ol>
                    <?php foreach($vendorContacts as $index=>$contact) { ?>
                    <li contact_id="<?php echo $contact->id; ?>"><?php echo $contact->firstName;?> <?php echo $contact->lastName;?><?php if ($contact->title) {?>, <?php echo $contact->title; }?><?php if ($contact->email || $contact->phone) { ?><br><?php echo $contact->email;?><?php if($contact->phone) {?>, Ph <?php echo $contact->phone;}}?>
                        <br />
                        <?php foreach($contact as $prop=>$val) {
                            echo '<input type="hidden" name="existing_contacts['.$index.']['.$prop.']" value="'.$val.'" />'."\n";
                        }
                        ?>
                        <a href="JavaScript:void(0)" onclick="deleteContact(<?php echo $contact->id; ?>)">Delete</a>
                    </li>
                    <?php } ?>
                </ol>
                <?php } ?>
