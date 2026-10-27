import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';

type Appliance = {
    id: number;
    appliance_type: string;
    model_number: string;
    serial_number: string;
    customer: { name: string; phone: string };
    warranty_policies: { purchase_date: string; warranty_expiry: string }[];
};

type Page<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Appliances', href: route('appliances.index') }];

export default function AppliancesIndex({ appliances }: { appliances: Page<Appliance> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appliances" />
            <main className="space-y-6 p-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Appliances</h1>
                    <Link href={route('appliances.create')} className="bg-primary text-primary-foreground rounded-md px-4 py-2 text-sm">
                        Register appliance
                    </Link>
                </div>
                <div className="overflow-x-auto rounded-lg border">
                    <table className="w-full text-left text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3">Customer</th>
                                <th className="p-3">Appliance</th>
                                <th className="p-3">Model / serial</th>
                                <th className="p-3">Warranty dates</th>
                            </tr>
                        </thead>
                        <tbody>
                            {appliances.data.map((appliance) => (
                                <tr key={appliance.id} className="border-t">
                                    <td className="p-3">
                                        {appliance.customer.name}
                                        <br />
                                        {appliance.customer.phone}
                                    </td>
                                    <td className="p-3">{appliance.appliance_type}</td>
                                    <td className="p-3">
                                        {appliance.model_number}
                                        <br />
                                        {appliance.serial_number}
                                    </td>
                                    <td className="p-3">
                                        {appliance.warranty_policies.map((policy) => (
                                            <div key={`${policy.purchase_date}-${policy.warranty_expiry}`}>
                                                {policy.purchase_date} – {policy.warranty_expiry}
                                            </div>
                                        ))}
                                    </td>
                                </tr>
                            ))}
                            {appliances.data.length === 0 && (
                                <tr>
                                    <td className="p-4" colSpan={4}>
                                        No appliances registered.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                <nav className="flex flex-wrap gap-2" aria-label="Pagination">
                    {appliances.links.map((link, index) =>
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
