import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { type FormEventHandler } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Appliances', href: route('appliances.index') },
    { title: 'Register appliance', href: route('appliances.create') },
];

export default function CreateAppliance() {
    const { data, setData, post, processing, errors } = useForm({
        customer_name: '',
        customer_phone: '',
        appliance_type: '',
        model_number: '',
        serial_number: '',
        purchase_date: '',
        warranty_expiry: '',
    });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('appliances.store'));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Register appliance" />
            <main className="mx-auto w-full max-w-3xl space-y-6 p-6">
                <h1 className="text-xl font-semibold">Register an appliance</h1>
                <form onSubmit={submit} className="grid gap-5 sm:grid-cols-2">
                    {(
                        [
                            ['customer_name', 'Customer name'],
                            ['customer_phone', 'Customer phone'],
                            ['appliance_type', 'Appliance type'],
                            ['model_number', 'Model number'],
                            ['serial_number', 'Serial number'],
                            ['purchase_date', 'Purchase date'],
                            ['warranty_expiry', 'Warranty expiry'],
                        ] as const
                    ).map(([field, label]) => (
                        <div className="grid gap-2" key={field}>
                            <Label htmlFor={field}>{label}</Label>
                            <Input
                                id={field}
                                type={field.endsWith('_date') || field === 'warranty_expiry' ? 'date' : 'text'}
                                value={data[field]}
                                onChange={(event) => setData(field, event.target.value)}
                                required
                            />
                            <InputError message={errors[field]} />
                        </div>
                    ))}
                    <div className="flex items-center gap-3 sm:col-span-2">
                        <Button disabled={processing}>Register appliance</Button>
                        <Link href={route('appliances.index')} className="text-sm underline">
                            Cancel
                        </Link>
                    </div>
                </form>
            </main>
        </AppLayout>
    );
}
