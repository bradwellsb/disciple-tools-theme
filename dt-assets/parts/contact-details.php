<?php

( function () {
    $contact = Disciple_Tools_Contacts::get_contact( get_the_ID(), true, true );
    $channel_list = Disciple_Tools_Contacts::get_channel_list();
    $current_user = wp_get_current_user();
    $contact_fields = Disciple_Tools_Contacts::get_contact_fields();

    function dt_contact_details_status( $id, $verified, $invalid ) { ?>
        <img id="<?php echo esc_html( $id )?>-verified" class="details-status" style="display:<?php echo esc_html( $verified )?>" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/verified.svg' )?>" />
        <img id="<?php echo esc_html( $id ) ?>-invalid" class="details-status" style="display:<?php echo esc_html( $invalid )?>" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/broken.svg' )?>" />
        <?php
    } ?>


    <!-- Requires update block -->
    <section class="cell small-12 update-needed-notification"
             style="display: <?php echo esc_html( ( isset( $contact['requires_update'] ) && $contact['requires_update'] === true ) ? "block" : "none" ) ?> ">
        <div class="bordered-box detail-notification-box" style="background-color:#F43636">
            <h4><img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/alert-circle-exc.svg' ) ?>"/><?php esc_html_e( 'This contact needs an update', 'disciple_tools' ) ?>.</h4>
            <p><?php esc_html_e( 'Please provide an update by posting a comment.', 'disciple_tools' )?></p>
        </div>
    </section>

    <!-- Assigned to block -->
    <?php
    if ( isset( $contact['overall_status'] ) && $contact['overall_status']['key'] == 'assigned' &&
        isset( $contact['assigned_to'] ) && $contact['assigned_to']['id'] == $current_user->ID ) { ?>
    <section class="cell accept-contact" id="accept-contact">
        <div class="bordered-box detail-notification-box">
            <h4><?php esc_html_e( 'This contact has been assigned to you.', 'disciple_tools' )?></h4>
            <button class="accept-button button small accept-decline" data-action="accept"><?php esc_html_e( 'Accept', 'disciple_tools' )?></button>
            <button class="decline-button button small accept-decline" data-action="decline"><?php esc_html_e( 'Decline', 'disciple_tools' )?></button>
        </div>
    </section>
    <?php } ?>

    <?php if ( isset( $contact['corresponds_to_user'] ) ) { ?>
    <section class="cell" id="contact-is-user">
        <div class="bordered-box detail-notification-box" style="background-color:#3F729B">
            <h4><?php esc_html_e( 'This contact represents a user.', 'disciple_tools' )?></h4>
        </div>
    </section>
    <?php } ?>

    <?php do_action( 'dt_contact_detail_notification', $contact ); ?>

    <section class="cell bordered-box">
        <div style="display: flex;">
            <div class="item-details-header" style="flex-grow:1">
                <i class="fi-torso large" style="padding-bottom: 1.2rem"></i>
                <span class="title"><?php the_title_attribute(); ?></span>
            </div>
            <div>
                <button class="" id="open-edit">
                    <i class="fi-pencil"></i>
                    <span><?php esc_html_e( 'Edit', 'disciple_tools' )?></span>
                </button>
            </div>
        </div>
        <div class="grid-x grid-margin-x" style="margin-top: 20px">
            <div class="cell small-12 medium-4">
                <div class="section-subheader">
                    <?php esc_html_e( "Status", 'disciple_tools' ) ?>
                    <button class="help-button" data-section="overall-status-help-text">
                        <img class="help-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/help.svg' ) ?>"/>
                    </button>
                </div>
                <?php
                $active_color = "#366184";
                $current_key = $contact["overall_status"]["key"] ?? "";
                if ( isset( $contact_fields["overall_status"]["default"][ $current_key ]["color"] )){
                    $active_color = $contact_fields["overall_status"]["default"][ $current_key ]["color"];
                }
                ?>
                <select id="overall_status" class="select-field color-select" style="margin-bottom:0px; background-color: <?php echo esc_html( $active_color ) ?>">
                    <?php foreach ($contact_fields["overall_status"]["default"] as $key => $option){
                        $value = $option["label"] ?? "";
                        if ( $contact["overall_status"]["key"] === $key ) {
                            ?>
                            <option value="<?php echo esc_html( $key ) ?>" selected><?php echo esc_html( $value ); ?></option>
                        <?php } else { ?>
                            <option value="<?php echo esc_html( $key ) ?>"><?php echo esc_html( $value ); ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <p>
                    <span id="reason">
                        <?php
                        $hide_edit_button = false;
                        if ( $contact["overall_status"]["key"] === "paused" &&
                             isset( $contact["reason_paused"]["label"] )){
                            echo '(' . esc_html( $contact["reason_paused"]["label"] ) . ')';
                        } else if ( $contact["overall_status"]["key"] === "closed" &&
                                    isset( $contact["reason_closed"]["label"] )){
                            echo '(' . esc_html( $contact["reason_closed"]["label"] ) . ')';
                        } else if ( $contact["overall_status"]["key"] === "unassignable" &&
                                    isset( $contact["reason_unassignable"]["label"] )){
                            echo '(' . esc_html( $contact["reason_unassignable"]["label"] ) . ')';
                        } else {
                            if ( !in_array( $contact["overall_status"]["key"], [ "paused", "closed", "unassignable" ] ) ){
                                $hide_edit_button = true;
                            }
                        }
                        ?>
                    </span>
                    <button id="edit-reason" <?php if ( $hide_edit_button ) : ?> style="display: none"<?php endif; ?> ><i class="fi-pencil"></i></button>
                </p>
            </div>

            <div class="cell small-12 medium-4">
                <!-- Assigned To -->
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/assigned-to.svg' ?>">
                    <?php esc_html_e( 'Assigned to', 'disciple_tools' )?>
                    <button class="help-button" data-section="assigned-to-help-text">
                        <img class="help-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/help.svg' ) ?>"/>
                    </button>
                </div>

                <div class="assigned_to details">
                    <var id="assigned_to-result-container" class="result-container assigned_to-result-container"></var>
                    <div id="assigned_to_t" name="form-assigned_to">
                        <div class="typeahead__container">
                            <div class="typeahead__field">
                                <span class="typeahead__query">
                                    <input class="js-typeahead-assigned_to input-height" dir="auto"
                                           name="assigned_to[query]" placeholder="<?php esc_html_e( "Search Users", 'disciple_tools' ) ?>"
                                           autocomplete="off">
                                </span>
                                <span class="typeahead__button">
                                    <button type="button" class="search_assigned_to typeahead__image_button input-height" data-id="assigned_to_t">
                                        <img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/chevron_down.svg' ) ?>"/>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cell small-12 medium-4">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/subassigned.svg' ?>">
                    <?php esc_html_e( 'Sub-assigned to', 'disciple_tools' )?>
                </div>
                <div class="subassigned details">
                    <var id="subassigned-result-container" class="result-container subassigned-result-container"></var>
                    <div id="subassigned_t" name="form-subassigned" class="scrollable-typeahead">
                        <div class="typeahead__container">
                            <div class="typeahead__field">
                                <span class="typeahead__query">
                                    <input class="js-typeahead-subassigned input-height"
                                           name="subassigned[query]" placeholder="<?php esc_html_e( "Search multipliers and contacts", 'disciple_tools' ) ?>"
                                           autocomplete="off">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr />


        <div class="display-fields grid-x grid-margin-x">
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <!--Phone-->
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/phone.svg' ?>">
                    <?php echo esc_html( $channel_list["phone"]["label"] ) ?>
                </div>
                <ul class="phone">
                </ul>
            </div>
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <!--Email-->
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/email.svg' ?>">
                    <?php echo esc_html( $channel_list['email']['label'] ) ?>
                </div>
                <ul class="email">
                </ul>
            </div>

            <!-- Social Media -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader"><?php esc_html_e( 'Social Media', 'disciple_tools' ) ?></div>
                <ul class="social"></ul>
            </div>

            <!-- Address -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/house.svg' ?>">
                    <?php esc_html_e( 'Address', 'disciple_tools' )?>
                </div>
                <ul class="address"></ul>
            </div>

            <!-- Location Grid -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/location.svg' ?>">
                    <?php esc_html_e( 'Locations', 'disciple_tools' ) ?>
                </div>
                <ul class="location_grid-list"></ul>
            </div>

            <!-- People Groups -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . "/dt-assets/images/people-group.svg" ?>">
                    <?php esc_html_e( 'People Groups', 'disciple_tools' )?>
                </div>
                <ul class="people_groups-list details-list"></ul>
            </div>


            <!-- Age -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . "/dt-assets/images/contact-age.svg" ?>">
                    <?php esc_html_e( 'Age', 'disciple_tools' )?>
                </div>
                <ul class="details-list">
                    <li class="age">
                        <?php
                        if ( !empty( $contact['age']['label'] ) ){
                            echo esc_html( $contact['age']['label'] );
                        } else {
                            esc_html_e( 'No age set', 'disciple_tools' );
                        }
                        ?>
                    </li>
                </ul>
            </div>

            <!-- Gender -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/gender.svg' ?>">
                    <?php esc_html_e( 'Gender', 'disciple_tools' )?>
                </div>
                <ul class="details-list">
                    <li class="gender">
                        <?php
                        if ( !empty( $contact['gender']['label'] ) ){
                            echo esc_html( $contact['gender']['label'] );
                        } else {
                            esc_html_e( 'No gender set', 'disciple_tools' );
                        }
                        ?>
                </ul>
            </div>

            <!-- Source -->
            <div class="xlarge-4 large-6 medium-6 small-12 cell">
                <div class="section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/source.svg' ?>">
                    <?php esc_html_e( 'Source' ); ?>
                </div>
                <ul class="sources-list <?php echo esc_html( user_can( get_current_user_id(), 'view_any_contacts' ) ? 'details-list' : '' ) ?>">
                    <?php
                    foreach ($contact['sources'] ?? [] as $value){
                        ?>
                        <li class="<?php echo esc_html( $value )?>">
                            <?php echo esc_html( $contact_fields['sources']['default'][$value]["label"] ?? $value ) ?>
                        </li>
                    <?php }
                    if ( !isset( $contact['sources'] ) || sizeof( $contact['sources'] ) === 0){
                        ?> <li id="no-source"><?php esc_html_e( "No source set", 'disciple_tools' ) ?></li><?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>

    <div class="reveal" id="contact-details-edit" data-reveal data-close-on-click="false">
        <h1><?php esc_html_e( "Edit Contact", 'disciple_tools' ) ?></h1>
        <div class="display-fields details-edit-fields">
            <div class="grid-x">
                <div class="cell section-subheader">
                    <?php esc_html_e( 'Name', 'disciple_tools' ) ?>
                </div>
                <input type="text" id="title" class="edit-text-input" dir="auto" value="<?php the_title_attribute(); ?>">

            </div>

            <!-- Phone -->
            <div class="grid-x">
                <div class="cell section-subheader">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/phone.svg' ?>">
                    <?php echo esc_html( $channel_list["phone"]["label"] ) ?>
                    <button data-list-class="contact_phone" class="add-button">
                        <img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/small-add.svg' ) ?>"/>
                    </button>
                </div>

                <ul id="edit-contact_phone" class="cell">

                </ul>
            </div>

            <!-- Email -->
            <div class="grid-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/email.svg' ?>">
                    <?php echo esc_html( $channel_list['email']['label'] ) ?>
                    <button data-list-class="contact_email" class="add-button">
                        <img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/small-add.svg' ) ?>"/>
                    </button>
                </div>
                <ul id="edit-contact_email" class="cell"></ul>
            </div>

            <!-- Address -->
            <div class="grix-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/house.svg' ?>">
                    <?php esc_html_e( 'Address', 'disciple_tools' )?>
                    <button id="add-new-address">
                        <img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/small-add.svg' ) ?>"/>
                    </button>
                </div>
                <!-- list of addresses -->
                <ul id="edit-contact_address" class="cell"></ul>
            </div>

            <div class="grix-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/location.svg' ?>">
                    <?php esc_html_e( 'Locations', 'disciple_tools' ) ?>
                </div>
                <div class="location_grid">
                    <var id="location_grid-result-container" class="result-container"></var>
                    <div id="location_grid_t" name="form-location_grid" class="scrollable-typeahead typeahead-margin-when-active">
                        <div class="typeahead__container">
                            <div class="typeahead__field">
                                <span class="typeahead__query">
                                    <input class="js-typeahead-location_grid"
                                           name="location_grid[query]" placeholder="<?php esc_html_e( "Search Locations", 'disciple_tools' ) ?>"
                                           autocomplete="off">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="grix-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/assigned-to.svg' ?>">
                    <?php esc_html_e( 'Social Media', 'disciple_tools' ) ?>
                    <button id="add-new-social-media">
                        <img src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/small-add.svg' ) ?>"/>
                    </button>
                </div>
                <ul id="edit-social" class="cell"></ul>
            </div>

            <!-- Sources -->
            <div class="grix-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/source.svg' ?>">
                    <?php esc_html_e( 'Source', "disciple_tools" ); ?>
                </div>
                <span id="sources-result-container" class="result-container"></span>
                <div id="sources_t" name="form-sources" class="scrollable-typeahead">
                    <div class="typeahead__container">
                        <div class="typeahead__field">
                            <span class="typeahead__query">
                                <input class="js-typeahead-sources"
                                       name="sources[query]" placeholder="<?php esc_html_e( "Search sources", 'disciple_tools' ) ?>"
                                       autocomplete="off">
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gender -->
            <div class="grid-x grid-margin-x">
                <div class="cell small-6">
                    <div class="section-subheader cell">
                        <img src="<?php echo esc_url( get_template_directory_uri() ) . '/dt-assets/images/gender.svg' ?>">
                        <?php esc_html_e( 'Gender', 'disciple_tools' )?>
                    </div>
                    <select id="gender" class="select-input">
                        <?php
                        foreach ( $contact_fields['gender']['default'] as $gender_key => $option ) {
                            $gender_value = $option["label"] ?? "";
                            if ( isset( $contact['gender']['key'] ) &&
                                 $contact['gender']['key'] === $gender_key){
                                echo '<option value="'. esc_html( $gender_key ) . '" selected>' . esc_html( $gender_value ) . '</option>';
                            } else {
                                echo '<option value="'. esc_html( $gender_key ) . '">' . esc_html( $gender_value ). '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Age -->
                <div class="cell small-6">
                    <div class="section-subheader cell">
                        <img src="<?php echo esc_url( get_template_directory_uri() ) . "/dt-assets/images/contact-age.svg" ?>">
                        <?php esc_html_e( 'Age', 'disciple_tools' )?>
                    </div>
                    <select id="age" class="select-input">
                        <?php
                        foreach ( $contact_fields["age"]["default"] as $age_key => $option ) {
                            $age_value = $option["label"] ?? "";
                            if ( isset( $contact["age"] ) && isset( $contact["age"]["key"] ) &&
                                 $contact["age"]["key"] === $age_key){
                                echo '<option value="'. esc_html( $age_key ) . '" selected>' . esc_html( $age_value ) . '</option>';
                            } else {
                                echo '<option value="'. esc_html( $age_key ) . '">' . esc_html( $age_value ). '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- People Groups -->
            <div class="grix-x">
                <div class="section-subheader cell">
                    <img src="<?php echo esc_url( get_template_directory_uri() ) . "/dt-assets/images/people-group.svg" ?>">
                    <?php esc_html_e( 'People Groups', 'disciple_tools' )?>
                </div>
                <div class="people_groups">
                    <var id="people_groups-result-container" class="result-container"></var>
                    <div id="people_groups_t" name="form-people_groups" class="scrollable-typeahead">
                        <div class="typeahead__container">
                            <div class="typeahead__field">
                                <span class="typeahead__query">
                                    <input class="js-typeahead-people_groups"
                                           name="people_groups[query]" placeholder="<?php esc_html_e( "Search People Groups", 'disciple_tools' ) ?>"
                                           autocomplete="off">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Buttons -->
        <div>
            <button class="button button-cancel clear" data-close aria-label="Close reveal" type="button">
                <?php esc_html_e( 'Cancel', 'disciple_tools' )?>
            </button>
            <button class="button loader" type="button" id="save-edit-details">
                <?php esc_html_e( 'Save', 'disciple_tools' )?>
            </button>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <!-- Address Reveal -->
    <div class="medium reveal geocode-address" id="geocode-address" data-reveal>
        <h1>Add Address</h1>

        <!-- address and validation-->
        <label for="validate_addressnew">Address</label>
        <div class="input-group">
            <input type="text"
                   placeholder="example: 1000 Broadway, Denver, CO 80126"
                   class="profile-input input-group-field contact-input"
                   name="validate_address"
                   id="validate_addressnew"
                   data-type="contact_address"
                   value=""
            />
            <div class="input-group-button">
                <input type="button" class="button"
                       onclick="validate_group_address( jQuery('#validate_addressnew').val(), 'new')"
                       value="Validate"
                       id="validate_address_buttonnew">
            </div>
        </div>
        <div id="possible-resultsnew">
            <input type="hidden" name="address" id="address_new" value=""/>
        </div>

        <!-- drill down -->
        <div id="location_grid-encode-contact">

        </div>

        <!-- map -->
        <div id="address-click-map"></div>
        <p>
            <button class="button" data-open="contact-details-edit" onclick="window.GEOCODEFUNCTIONS.getAddressInput()">Select</button>
            <button class="button" data-open="contact-details-edit" data-close>Cancel</button>
        </p>
    </div>



<?php } )(); ?>
