

<div class="alert alert-info mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="mr-4 text-center">
            <i class="fa fa-question-circle fa-2x"></i>
        </div>
        <div>
            <h3>
                Route Server Filtering
            </h3>
            <p>
                <b>IXP Manager</b> supports the industry standards for community based route server filtering. You can find
                the <a href="https://docs.ixpmanager.org/latest/features/route-servers/#well-known-filtering-communities">official
                    documentation here</a>. Using the BGP-community mechanism can be difficult to implement where a network
                engineer is not familiar with BGP communities or where a network may have arduous change control processes
                for altering a router's configuration.
            </p>
            <p>
                This purpose of this tool is to allow IXP participants to implement the exact same mechanism but rather than
                tagging your routes on egress from your router / manipulating routes on ingress to your router, the IXP's
                route servers perform the equivalent tagging / route manipulation as they accept your routes from you and/or
                send your routes to other networks.
            </p>
            <p>
                Please note the following important points:
            </p>
            <ol>
                <li>
                    This tool is intended to help you make relatively simple routing policies.
                </li>
                <li>
                    When processing routes, please consider the ordering of your rules
                    and ensure to put more specific rules first.
                </li>
                <li>
                    You are responsible for your own routing policy and ensuring any rules you set here have the desired effect.
                    If in doubt, feel free to contact our operations team.
                </li>
            </ol>
        </div>
    </div>
</div>
