

<div class="alert alert-info mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="mr-4 text-center">
            <i class="fa fa-question-circle fa-2x"></i>
        </div>
        <div>
            <h3>
                Route Server Filtering at INEX
            </h3>
            <p>
                At INEX, we helped define the industry standards for community based route server filtering. You can find
                the <a href="https://www.inex.ie/technical/route-servers/">supported communities here</a>.
            </p>
            <p>
                We know that using the BGP-community mechanism can be difficult to implement where a network
                engineer is not familiar with BGP communities; or where a network may have arduous change control processes
                for altering a router's configuration; or especially when you need to make changes in an emergency scenario.
            </p>
            <p>
                The purpose of this tool is to give our members access to the exact same mechanism via a friendlier user
                interface. Rather than tagging your routes on egress from your router / manipulating routes on ingress to your router, INEX's
                route servers perform the equivalent tagging / route manipulation as they accept your routes from you and/or
                send your routes to other networks.
            </p>
            <p>
                Please note the following important points:
            </p>
            <ol>
                <li>
                    Above all else, if you have any questions please <a href="<?= route( 'public-content', 'support' ) ?>">just ask us</a>.
                </li>
                <li>
                    Changes should be live within ten minutes. Each route server pair pulls fresh configuration at least once every 10 minutes. You can see
                    the last reconfigure times for each router on <a href="<?= route('lg::index') ?>">the looking glasses</a>.
                </li>
                <li>
                    This tool is intended to help you make relatively simple routing policies.
                </li>
                <li>
                    When processing routes, please consider the ordering of your rules
                    and ensure to put more specific rules first.
                </li>
                <li>
                    You are responsible for your own routing policy and ensuring any rules you set here have the desired effect.
                    Again, if in doubt, feel free to contact our operations team.
                </li>
            </ol>
        </div>
    </div>
</div>
