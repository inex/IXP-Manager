
            # Filter for Facebook as per INEX ticket #12919
            export filter {
                if (bgp_path.first = 6939 ) then {
                    reject;
                }

                accept;
            };
