<div class="panel panel-default">
  <div class="panel-heading">
    <div class="pull-right"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-calendar"></i> <i class="caret"></i></a>
      <ul id="range-visitor" class="dropdown-menu dropdown-menu-right">
        <li><a href="day"><?php echo $text_day; ?></a></li>
        <li><a href="week"><?php echo $text_week; ?></a></li>
        <li class="active"><a href="month"><?php echo $text_month; ?></a></li>
        <li><a href="year"><?php echo $text_year; ?></a></li>
      </ul>
    </div>
    <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> <?php echo $heading_title; ?></h3>
  </div>
  <div class="panel-body">
      <div id="" >
          <div id="reportVisitor" style="width: 100%; height: 159px;"></div>
          <div>
              <div class='row' style='margin-top:20px;'>
                  <div class='col-sm-6'>
                      <h5>Selection</h5>
                      <ul>
                          <li>Visitors : <span id='totalVisitor'></span></li>
                          <li>Hits : <span id='totalHits'></span></li>
                      </ul>
                  </div>
                  
                  <div class='col-sm-6'>
                      <h5>Total Records</h5>
                      <ul>
                          <li>Visitors : <?php echo $total_visitors;?></li>
                          <li>Hits : <?php echo $total_hits;?></li>
                      </ul>
                  </div>
              </div>
              <div class='row text-right'>
                  <div class='col-sm-12'>
                      <a href='?route=report/web_visitor&token=<?php echo $token;?>'>View detail</a>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script> 
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script>

<script>
    $(function(){
        $('#range-visitor a').on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().find('li').removeClass('active');
            $(this).parent().addClass('active');
            $.ajax({
                    type: 'GET',
                    url: 'index.php?route=dashboard/visitor/chartvisitor&token=<?php echo $token; ?>&range=' + $(this).attr('href'),
                    dataType: 'json',
                    success: function(json) {
                            var option = {
                                    shadowSize: 0,
                                    lines: {
                                            show: true,
                                            fill: true,
                                            lineWidth: 1
                                    },
                                    grid: {
                                            backgroundColor: '#FFFFFF'
                                    },
                                    xaxis: {
                            ticks: json.xaxis
                                    }
                            };
                            $.plot($('#reportVisitor'), [json.order, json.customer], option);
                            $('#totalVisitor').html(json.noVisitor);
                            $('#totalHits').html(json.noHit);
                    }
            });
        });
        
        $('#range-visitor .active a').trigger('click');
    });
</script>