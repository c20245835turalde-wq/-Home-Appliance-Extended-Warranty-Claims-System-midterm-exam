import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { type FormEventHandler } from 'react';

type Policy = {
    id: number;
    purchase_date: string;
    warranty_expiry: string;
    appliance: {
        appliance_type: string;
        model_number: string;
        serial_number: string;
        customer: { name: string };
    };
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Warranty claims', href: route('warranty-claims.index') },
    { title: 'File claim', href: route('warranty-claims.create') },
];

export default function CreateWarrantyClaim({ policies }: { policies: Policy[] }) {
    const { data, setData, post, processing, errors } = useForm({
        warranty_policy_id: '',
        claim_date: '',
        defect_description: '',
        status: 'pending',
        approved_amount: '',
    });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('warranty-claims.store'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="File warranty claim" />
            <main className="mx-auto w-full max-w-3xl space-y-6 p-6">
                <h1 className="text-xl font-semibold">File a warranty claim</h1>
                <form onSubmit={submit} className="grid gap-5">
                    <div className="grid gap-2">
                        <Label htmlFor="warranty_policy_id">Appliance and warranty</Label>
                        <select
                            id="warranty_policy_id"
                            value={data.warranty_policy_id}
                            onChange={(event) => setData('warranty_policy_id', event.target.value)}
                            className="bg-background h-10 rounded-md border px-3 text-sm"
                            required
                        >
                            <option value="">Select a policy</option>
                            {policies.map((policy) => (
                                <option key={policy.id} value={policy.id}>
                                    {policy.appliance.customer.name} — {policy.appliance.appliance_type} {policy.appliance.model_number} /{' '}
                                    {policy.appliance.serial_number} ({policy.purchase_date} to {policy.warranty_expiry})
                                </option>
                            ))}
                        </select>
                        <InputError message={errors.warranty_policy_id} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="claim_date">Claim date</Label>
                        <Input
                            id="claim_date"
                            type="date"
                            value={data.claim_date}
                            onChange={(event) => setData('claim_date', event.target.value)}
                            required
                        />
                        <InputError message={errors.claim_date} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="defect_description">Defect description</Label>
                        <textarea
                            id="defect_description"
                            value={data.defect_description}
                            onChange={(event) => setData('defect_description', event.target.value)}
                            className="bg-background min-h-28 rounded-md border p-3 text-sm"
                            required
                        />
                        <InputError message={errors.defect_description} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="status">Status</Label>
                        <select
                            id="status"
                            value={data.status}
                            onChange={(event) => setData('status', event.target.value)}
                            className="bg-background h-10 rounded-md border px-3 text-sm"
                            required
                        >
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <InputError message={errors.status} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="approved_amount">Approved amount</Label>
                        <Input
                            id="approved_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            value={data.approved_amount}
                            onChange={(event) => setData('approved_amount', event.target.value)}
                        />
                        <InputError message={errors.approved_amount} />
                    </div>
                    <div className="flex items-center gap-3">
                        <Button disabled={processing || policies.length === 0}>File claim</Button>
                        <Link href={route('warranty-claims.index')} className="text-sm underline">
                            Cancel
                        </Link>
                    </div>
                    {policies.length === 0 && (
                        <p className="text-muted-foreground text-sm">Register an appliance and warranty before filing a claim.</p>
                    )}
                </form>
            </main>
        </AppLayout>
    );
}
