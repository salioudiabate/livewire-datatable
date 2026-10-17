{{--
    Card layout below mobileCardsBreakpoint() (HasMobileCards). Pure CSS on the regular markup: each
    cell carries its column label in data-label; data-dt-cell marks the title, checkbox and actions cells.
--}}
@if ($this->mobileCards())
    <style>
        @media (max-width: {{ $this->mobileCardsBreakpoint() - 0.02 }}px) {
            .dt-cards .dt-table-wrapper { background: #f8fafc; padding: 2px 0; }
            .dt-cards .dt-table-wrapper table { display: block; width: 100%; min-width: 0 !important; }
            .dt-cards .dt-table-wrapper thead { display: block; background: transparent; border: 0; }
            .dt-cards .dt-table-wrapper thead tr { display: flex; border: 0; }
            .dt-cards .dt-table-wrapper thead th { display: none; }
            .dt-cards .dt-table-wrapper thead th[data-dt-cell="checkbox"] { display: block; padding: 8px 22px 2px; background: transparent !important; position: static !important; }
            .dt-cards .dt-table-wrapper tbody { display: block; }
            .dt-cards .dt-table-wrapper tbody tr { display: flex; flex-direction: column; position: relative; margin: 8px 10px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
            .dt-cards .dt-table-wrapper tbody td { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; min-width: 0; padding: 3px 0 !important; border: 0 !important; text-align: right; white-space: normal !important; overflow-wrap: anywhere; position: static !important; left: auto !important; background: transparent !important; box-shadow: none !important; }
            .dt-cards .dt-table-wrapper tbody td::before { content: attr(data-label); flex: 0 0 auto; max-width: 45%; text-align: left; font-size: 12px; font-weight: 500; color: #64748b; }
            .dt-cards .dt-table-wrapper tbody td[data-label=""]::before { content: none; }
            .dt-cards .dt-table-wrapper tbody td[data-dt-card-hidden] { display: none; }
            .dt-cards .dt-table-wrapper tbody td[data-dt-cell="title"] { display: block; order: -1; margin-bottom: 4px; padding: 0 36px 6px 0 !important; border-bottom: 1px solid #f1f5f9 !important; text-align: left; font-size: 15px; }
            .dt-cards .dt-table-wrapper tbody td[data-dt-cell="title"]::before { content: none; }
            .dt-cards .dt-table-wrapper tbody tr:has(td[data-dt-cell="checkbox"]) td[data-dt-cell="title"] { padding-left: 28px !important; }
            .dt-cards .dt-table-wrapper tbody td[data-dt-cell="checkbox"] { display: block; position: absolute !important; top: 12px; left: 12px; padding: 0 !important; }
            .dt-cards .dt-table-wrapper tbody td[data-dt-cell="actions"] { display: block; position: absolute !important; top: 5px; right: 6px; padding: 0 !important; }
            .dt-cards .dt-table-wrapper tbody td[colspan] { display: block; text-align: center; }
        }
    </style>
@endif
