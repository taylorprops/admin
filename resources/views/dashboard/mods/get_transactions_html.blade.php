<div class="row">

    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4">

        <div class="bg-white text-gray p-3 rounded">

            <div class="d-flex justify-content-around align-items-center">
                <div class=" pl-2">
                    <div class="text-orange font-13">Active Listings</div>
                </div>
                <div class="d-flex justify-content-around align-items-center font-14 bg-blue-light text-primary w-20 mb-2 p-2 rounded">
                    {{ $active_listings_count }}
                </div>
            </div>

            <div class="d-flex justify-content-around align-items-center">
                <a href="/agents/doc_management/transactions?tab=listings" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                <a href="/agents/doc_management/transactions/add/listing" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
            </div>


        </div>

    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 mt-3 mt-sm-0">

        <div class="bg-white text-gray p-3 rounded">

            <div class="d-flex justify-content-around align-items-center">
                <div class=" pl-2">
                    <div class="text-orange font-13">Active Contracts</div>
                </div>
                <div class="d-flex justify-content-around align-items-center font-14 bg-blue-light text-primary w-20 mb-2 p-2 rounded">
                    {{ $active_contracts_count }}
                </div>
            </div>

            <div class="d-flex justify-content-around align-items-center">
                <a href="/agents/doc_management/transactions?tab=contracts" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                <a href="/agents/doc_management/transactions/add/contract" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
            </div>


        </div>

    </div>

    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 mt-3 mt-md-0 mt-lg-3 mt-xl-0">

        <div class="bg-white text-gray p-3 rounded">

            <div class="d-flex justify-content-around align-items-center">
                <div class=" pl-2">
                    <div class="text-orange font-13">Pending Referrals</div>
                </div>
                <div class="d-flex justify-content-around align-items-center font-14 bg-blue-light text-primary w-20 mb-2 p-2 rounded">
                    {{ $active_referrals_count }}
                </div>
            </div>

            <div class="d-flex justify-content-around align-items-center">
                <a href="/agents/doc_management/transactions?tab=referrals" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                <a href="/agents/doc_management/transactions/add/referral" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
            </div>


        </div>

    </div>

</div>
