import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';

type Claim = {
    id: number;
    claim_date: string;
    defect_description: string;
    status: string;
    warranty_policy: {
        appliance: {
            customer: { name: string };
            appliance_type: string;
            model_number: string;
            serial_number: string;
        };
    };
    payout: { amount: string } | null;
};

type Page<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Warranty claims', href: route('warranty-claims.index') }];

export default function WarrantyClaimsIndex({ claims }: { claims: Page<Claim> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Warranty claims" />
            <main className="space-y-6 p-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Warranty claims</h1>
                    <Link href={route('warranty-claims.create')} className="bg-primary text-primary-foreground rounded-md px-4 py-2 text-sm">
                        File claim
                    </Link>
                </div>
                <div className="overflow-x-auto rounded-lg border">
                    <table className="w-full text-left text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3">Customer / appliance</th>
                                <th className="p-3">Claim date</th>
                                <th className="p-3">Defect</th>
                                <th className="p-3">Status</th>
                                <th className="p-3">Approved amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            {claims.data.map((claim) => (
                                <tr key={claim.id} className="border-t">
                                    <td className="p-3">
                                        {claim.warranty_policy.appliance.customer.name}
                                        <br />
                                        {claim.warranty_policy.appliance.appliance_type} {claim.warranty_policy.appliance.model_number} /{' '}
                                        {claim.warranty_policy.appliance.serial_number}
                                    </td>
                                    <td className="p-3">{claim.claim_date}</td>
                                    <td className="p-3">{claim.defect_description}</td>
                                    <td className="p-3 capitalize">{claim.status}</td>
                                    <td className="p-3">{claim.payout?.amount ?? '—'}</td>
                                </tr>
                            ))}
                            {claims.data.length === 0 && (
                                <tr>
                                    <td className="p-4" colSpan={5}>
                                        No claims filed.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                <nav className="flex flex-wrap gap-2" aria-label="Pagination">
                    {claims.links.map((link, index) =>
                        link.url ? (
                            <Link
                                key={`${link.label}-${index}`}
                                href={link.url}
                                className={`rounded border px-3 py-1 text-sm ${link.active ? 'bg-muted font-semibold' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : null,
                    )}
                </nav>
            </main>
        </AppLayout>
    );
}
