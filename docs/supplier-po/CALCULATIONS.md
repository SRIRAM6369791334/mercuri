# Supplier Purchase Order: Financial Math & Calculations Specification

## Core Principles
1. **Never Trust Client-Submitted Totals**: The frontend provides responsive live visual recalculation for user feedback, but `PurchaseOrderController` and `PurchaseOrderTotals` strictly recompute all line items and order totals server-side during `savePo` and `editPo`.
2. **Consistent Two-Decimal Rounding**: All currency and tax calculations round to 2 decimal places using standard half-up arithmetic (`round($value, 2)`).
3. **No Allowed Discount Restraints**: Unlike customer sales quotations which restrict sales engineers to preset discount ceilings, Supplier Purchase Orders represent direct negotiated procurement; any discount between 0.00% and 100.00% is valid.

---

## 1. Line Item Formulas

For each product row $i$:
$$\text{line\_base}_i = \text{round}(\text{qty}_i \times \text{unit\_price}_i, 2)$$

$$\text{line\_discount}_i = \text{round}\left(\text{line\_base}_i \times \frac{\text{discount\_pct}_i}{100}, 2\right)$$

$$\text{after\_line\_discount}_i = \max(0, \text{line\_base}_i - \text{line\_discount}_i)$$

If an overall order discount percentage $\text{overall\_discount\_pct}$ exists:
$$\text{overall\_discount\_amount} = \text{round}\left(\left(\sum \text{after\_line\_discount}_i\right) \times \frac{\text{overall\_discount\_pct}}{100}, 2\right)$$
The overall discount is allocated proportionally across rows with remainder reconciliation on the final line:
$$\text{line\_overall\_discount}_i = \text{round}\left(\text{overall\_discount\_amount} \times \frac{\text{after\_line\_discount}_i}{\sum \text{after\_line\_discount}}, 2\right)$$

The line taxable amount is:
$$\text{line\_taxable}_i = \max(0, \text{line\_after\_discount}_i - \text{line\_overall\_discount}_i)$$

GST amount for the row:
$$\text{line\_tax}_i = \text{round}\left(\text{line\_taxable}_i \times \frac{\text{gst\_pct}_i}{100}, 2\right)$$

Line Total (including tax):
$$\text{line\_total}_i = \text{line\_taxable}_i + \text{line\_tax}_i$$

---

## 2. Order Summary & Net PO Value

Gross Subtotal (Basic Total):
$$\text{Gross Subtotal} = \sum_{i} \text{line\_base}_i$$

Total Line Discount:
$$\text{Total Line Discount} = \sum_{i} \text{line\_discount}_i$$

Total Taxable Value:
$$\text{Taxable Total} = \sum_{i} \text{line\_taxable}_i$$

Total GST:
$$\text{GST Total} = \sum_{i} \text{line\_tax}_i$$

Raw Net Total:
$$\text{Raw Net Total} = \text{Taxable Total} + \text{GST Total} + \text{Freight Charges} + \text{Unloading Charges}$$

Rounded Grand Total (Net PO Value):
$$\text{Grand Total} = \text{round}(\text{Raw Net Total})$$

Round Off (₹):
$$\text{Round Off} = \text{round}(\text{Grand Total} - \text{Raw Net Total}, 2)$$

---

## 3. Commercial Advance Calculation

The advance percentage is derived from the standard Commercial Payment Terms:
- `20% Advance`: $20\%$
- `30% Advance`: $30\%$
- `50% Advance`: $50\%$
- `100% Against PI`: $100\%$
- `Credit 30 Days`: $0\%$

$$\text{Advance Amount} = \text{round}\left(\text{Grand Total} \times \frac{\text{advance\_pct}}{100}, 2\right)$$

---

## 4. Indian Currency Number-to-Words

The system renders currency words in standard Indian numbering format (Lakhs and Crores) via `\App\Helpers\IndianNumberHelper::numberToIndianWords()`:
- Integer amount converted into Crores, Lakhs, Thousands, Hundreds, and Units.
- Fractional paise formatted as `"and XX/100 Paise Only"`.
- Example: `₹1,24,500.00` $\rightarrow$ `"Rupees One Lakh Twenty Four Thousand Five Hundred Only"`.
