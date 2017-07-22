<script type="text/template" id="ae-report-loop">
    <div class="form-group-work-place">
        <div class="info-avatar-report">
            <a href="#" class="avatar-employer-report">
               {{=avatar}}
            </a>
            <div class="info-report">
                <span class="name-report">{{= display_name }}</span>
                <div class="date-chat-report">
                    {{= message_time }}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="content-report-wrapper">
            <div class="content-report">
                {{= comment_content }}
            </div>
            <# if(file_list){ #>
            <div class="title-attachment"><?php _e("Attachment", ET_DOMAIN); ?></div>
            {{= file_list }}
            <# } #>
        </div>
    </div>
</script>

<div class="modal fade" id="acceptance_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title">
                    <?php _e("Bid acceptance", ET_DOMAIN) ?>
                </h4>
            </div>
            <div class="modal-body">
              <?php
                $alipay_setting =  ae_get_option('escrow_alipay_api');
                $alipay_partner_id = $alipay_setting['alipay_partner_id'];
              //var_dump($alipay_setting);
              if($alipay_partner_id){ ?>
                <form role="form" id="escrow_bid" action="/alipay/alipayapi.php" class="">
                  <div class="escrow-info">
                          <!-- bid info content here -->
                          </div>
              <?php  do_action('fre_after_accept_bid_infor'); ?>
                <div class="form-group">
                            <button type="submit" class="btn-submit btn-sumary btn-sub-create">
                                <?php _e('Accept Bid - AliPay', ET_DOMAIN) ?>
                            </button>
                    <?php } else { ?>
                      <form role="form" id="escrow_bid" class="">
                        <div class="escrow-info">
                                <!-- bid info content here -->
                                </div>
                         <?php  do_action('fre_after_accept_bid_infor'); ?>
                        <div class="form-group">
                          <button type="submit" class="btn-submit btn-sumary btn-sub-create">
                            <?php _e('Accept Paypal Bid', ET_DOMAIN) ?>
                          </button>
                        </div>
                    <?php } ?>
                        </div>
                    </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL BID acceptance PROJECT-->
<script type="text/template" id="bid-info-template">
    <label style="line-height:2.5;"><?php _e( 'You are about to accept this bid for' , ET_DOMAIN ); ?></label>
    <p><strong class="color-green">{{=budget}}</strong><strong class="color-green"><i class="fa fa-check"></i></strong></p>
    <br>
    <label style="line-height:2.5;"><?php _e( 'You have to pay' , ET_DOMAIN ); ?><br></label>

    <# if(commission){ #>
    <p class="text-credit-small"><?php _e( 'Commission' , ET_DOMAIN ); ?> &nbsp;
        <strong style="color: #1faf67;">{{= commission }}</strong>
    </p>
    <# } #>
    <p class="text-credit-small"><?php _e( 'Total' , ET_DOMAIN ); ?> &nbsp;
        <strong style="color:#e74c3c;">{{=total}}</strong>
    </p>
    <br>
</script>
<script type="text/template" id="bid-info-template-alipay">
	<label style="line-height:2.5;"><?php _e( 'You are about to accept this bid for' , ET_DOMAIN ); ?></label>
	<p class="bid-currency"><strong class="text-green-dark">{{=budget}}</strong><strong class="color-green"><i class="fa fa-check"></i></strong></p>
	<br>
	<label style="line-height:2.5;"><?php _e( 'You have to pay' , ET_DOMAIN ); ?><br></label>
	<# if(commission){ #>
	<p class="text-credit-small"><span class="info-bid"><?php _e( 'Commission' , ET_DOMAIN ); ?></span>
		<strong style="color: #1faf67;">{{= commission }}</strong>
	</p>
	<# } #>
	<p class="text-credit-small"><span class="info-bid"><?php _e( 'Total' , ET_DOMAIN ); ?></span>
		<strong class="text-orange-dark">{{=total}}</strong>
	</p>
	<br>
	<input type="hidden" name="WIDout_trade_no" id="out_trade_no" value="{{=bid_id}}"/>
	<input type="hidden" name="WIDsubject" id="WIDsubject" value="payment"/>
	<input type="hidden" name="WIDtotal_fee" id="WIDtotal_fee" value="{{=budget_transfer}}"/>
	<input type="hidden" name="WIDbody" id="WIDbody" value="payment"/>

	<input type="hidden" name="bid_id" id="bid_id" value="{{=bid_id}}"/>

</script>
