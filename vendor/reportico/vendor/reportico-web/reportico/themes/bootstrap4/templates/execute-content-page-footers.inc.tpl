                {% autoescape false %}
                <div class="reportico-page-footer-block {{ PRINT_FORMAT }}" style="min-height: {{ PAGE_BOTTOM_MARGIN }}; overflow: hidden; bottom: 10;" >
                
				<!--<div class="reportico-page-footer-block {{ PRINT_FORMAT }}">-->
                    <div class="reportico-page-footerBody" style="min-height: {{ PAGE_BOTTOM_MARGIN }};  margin-left: {{ PAGE_LEFT_MARGIN }}; margin-right: {{ PAGE_RIGHT_MARGIN }};">
					<!--<div class="reportico-page-footerBody" >-->

                        {% for footer in CONTENT.pagefooters %}
                        <div class="reportico-page-footer" style="{{ footer.styles }}">
						<!--<div class="reportico-page-footer">-->
                            {% if ( footer.image ) %}
                            <img src='{{ footer.image}}' style="{{ footer.imagestyles }}">
                            {% endif %}
                            {{ footer.content }}
                        </div>
                        {% endfor %}
                
                    </div>
                </div>
                {% endautoescape %}
